#!/usr/bin/env escript
%% -*- erlang -*-
%%! -pa ./ebin -pa ./deps/jsx/ebin -pa ./deps/erlsom/ebin

-module(test1).
-import(io, [format/2]).

-include_lib("../src/kaltura_client.hrl").

main(_) ->
    application:start(inets),
    
    ClientConfiguration = #kaltura_configuration{
    	client_options = [{verbose, debug}]
    }, 
    ClientRequest = #kaltura_request{
    	ks = <<"KS Place Holder">>
    },
    Entry = #kaltura_media_entry{name = <<"test entry">>, mediaType = 2},
    Results = kaltura_media_service:add(ClientConfiguration, ClientRequest, Entry),

	io:format("Created entry: ~p~n", [Results]).
	