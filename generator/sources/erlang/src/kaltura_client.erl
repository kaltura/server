%% ----------------------------------------------------------------------------
%%                           _  __     _ _
%%                          | |/ /__ _| | |_ _  _ _ _ __ _
%%                          | ' </ _` | |  _| || | '_/ _` |
%%                          |_|\_\__,_|_|\__|\_,_|_| \__,_|
%%
%% This file is part of the Kaltura Collaborative Media Suite which allows users
%% to do with audio, video, and animation what Wiki platfroms allow them to do with
%% text.
%%
%% Copyright (C) 2006-2011  Kaltura Inc.
%%
%% This program is free software: you can redistribute it and/or modify
%% it under the terms of the GNU Affero General Public License as
%% published by the Free Software Foundation, either version 3 of the
%% License, or (at your option) any later version.
%%
%% This program is distributed in the hope that it will be useful,
%% but WITHOUT ANY WARRANTY; without even the implied warranty of
%% MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
%% GNU Affero General Public License for more details.
%%
%% You should have received a copy of the GNU Affero General Public License
%% along with this program.  If not, see <http://www.gnu.org/licenses/>.
%%
%% ----------------------------------------------------------------------------

-module(kaltura_client).
                   
-export([request/3]).
-export([add_params/2, add_params/3]).

-include_lib("src/kaltura_client.hrl").
     
-type reason()       :: term().
-type property()     :: atom() | tuple().
-type proplist()     :: [property()].
-type body()         :: proplist().
-type results()       :: proplist() | integer() | string() | boolean().
-type error_args()   :: proplist().
-type response()     :: {ok, SessionId::integer(), ServerName::string(), Results::results()} |
                        {server_error, SessionId::integer(), ServerName::string(), Code::string(), Message::string(), Args::error_args()} |
                        {client_error, Reason::reason()}.

%%% API ========================================================================

add_params(_, []) -> [];
add_params(Params, [{_, null}]) ->
	Params;
add_params(Params, [Param]) ->
	[Param | Params];
add_params(Params, [{_, null}|T]) ->
	add_params(Params, T);
add_params(Params, [Param|T]) ->
	add_params([Param | Params], T).
add_params(Params, Key, KalturaObject) when is_tuple(KalturaObject) ->
	PropList = kaltura_object_to_proplist(KalturaObject),
	Value = add_params([], PropList),
	add_params(Params, [{Key, Value}]);
add_params(Params, Key, Value) ->
	add_params(Params, [{Key, Value}]).

-spec request(ClientConfiguration::#kaltura_configuration{}, ClientRequest::#kaltura_request{}, Params::body()) -> Response::response().
request(ClientConfiguration, ClientRequest, Params) ->
	Params1 = add_params(Params, kaltura_request_to_proplist(ClientRequest)),
	Params2 = add_params(Params1, [{format, 1}]),
    
    request(ClientConfiguration, Params2).

%%% INTERNAL ===================================================================

request(ClientConfiguration, Body) ->
	Url = ClientConfiguration#kaltura_configuration.url,
    Headers = [{"Accept", "application/json"}, {"Content-Type", "application/json"}],
    Request = get_request(Url, Headers,  Body),
    httpc:set_options(ClientConfiguration#kaltura_configuration.client_options),
    Response = parse_response(httpc:request(post, Request, ClientConfiguration#kaltura_configuration.request_options, [{body_format, binary}])),
	case Response of
		{ok, _, _, Results} ->
			Results;
		{server_error, _, _, _, Message, _} ->
			throw(Message);
        {client_error, Reason} ->
			throw(Reason)
	end.

get_request(Url, Headers, []) ->
    {Url, Headers};
get_request(Url, Headers, Body) ->
    SendBody = jsx:to_json(Body),
    {Url, Headers, "application/json", SendBody}.

parse_response({ok, {_, Headers, Body}}) ->
	SessionId = proplists:get_value("x-kaltura-session", Headers),
	ServerName = proplists:get_value("x-me", Headers),
    Results = parse_body(Body),
    Code = proplists:get_value(<<"code">>, Results, null),
    Message = proplists:get_value(<<"message">>, Results, null),
    if
    	Code == null ->
    		{ok, SessionId, ServerName, Results};
    	Message == null ->
    		{ok, SessionId, ServerName, Results};
    	true ->
    		Args = proplists:get_value(<<"args">>, Results),
    		{server_error, SessionId, ServerName, Code, Message, Args}
    end;
parse_response({error, Reason}) ->
    {client_error, Reason}.

parse_body([])                    -> [];
parse_body(<<>>)                  -> [];
parse_body(Body) -> jsx:decode(Body).
