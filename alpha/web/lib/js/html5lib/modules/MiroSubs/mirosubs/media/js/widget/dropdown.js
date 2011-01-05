// Universal Subtitles, universalsubtitles.org
//
// Copyright (C) 2010 Participatory Culture Foundation
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as
// published by the Free Software Foundation, either version 3 of the
// License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Affero General Public License for more details.
//
// You should have received a copy of the GNU Affero General Public License
// along with this program.  If not, see
// http://www.gnu.org/licenses/agpl-3.0.html.

goog.provide('mirosubs.widget.DropDown');

/**
 * @constructor
 * @param {mirosubs.widget.DropDownContents} dropDownContents
 */
mirosubs.widget.DropDown = function(videoID, dropDownContents, videoTab) {
    goog.ui.Component.call(this);

    this.videoID_ = videoID;
    this.setStats_(dropDownContents);
    this.videoTab_ = videoTab;
    this.subtitleState_ = null;
    this.shown_ = false;
    this.languageClickHandler_ = new goog.events.EventHandler(this);
};

goog.inherits(mirosubs.widget.DropDown, goog.ui.Component);

mirosubs.widget.DropDown.Selection = {
    ADD_LANGUAGE: "add_language",
    IMPROVE_SUBTITLES: "improve_subtitles",
    SUBTITLE_HOMEPAGE: "subtitle_homepage",
    DOWNLOAD_SUBTITLES: "download_subtitles",
    CREATE_ACCOUNT: "create_account",
    LANGUAGE_PREFERENCES: "language_preferences",
    SUBTITLES_OFF: "subtitles_off",
    LANGUAGE_SELECTED: "language_selected",
    USERNAME: "username",
    LOGOUT: "logout"
};

mirosubs.widget.DropDown.prototype.getSubtitleCount = function() {
    return this.subtitleCount_;
};
mirosubs.widget.DropDown.prototype.hasSubtitles = function() {
    return this.subtitleCount_ > 0 || this.translationLanguages_.length > 0;
};
mirosubs.widget.DropDown.prototype.setStats_ = function(dropDownContents) {
    this.subtitleCount_ = dropDownContents.SUBTITLE_COUNT;
    this.translationLanguages_ = dropDownContents.TRANSLATIONS;;    
};
mirosubs.widget.DropDown.prototype.updateContents = function(dropDownContents) {
    this.setStats_(dropDownContents);    
    this.updateSubtitleStats_();
    this.addTranslationLinkListeners_();
    this.setCurrentSubtitleState(this.subtitleState_);
};

mirosubs.widget.DropDown.prototype.setCurrentSubtitleState = function(subtitleState) {
    this.clearCurrentLang_();
    this.subtitleState_ = subtitleState;
    this.setCurrentLangClassName_();
    goog.style.showElement(
        this.improveSubtitlesLink_,
        subtitleState != null);
};

mirosubs.widget.DropDown.prototype.createDom = function() {
    mirosubs.widget.DropDown.superClass_.createDom.call(this);
    var $d = goog.bind(this.getDomHelper().createDom, this.getDomHelper());
    this.getElement().className = 'mirosubs-dropdown';
    
    var languageListContainer = this.createLanguageList_($d);
    this.createActionList_($d);

    this.updateSubtitleStats_();
    this.updateActions_();
    
    this.getElement().appendChild(languageListContainer);
    this.getElement().appendChild(this.actions_);
};

mirosubs.widget.DropDown.prototype.createLanguageList_ = function($d) {
    var container = $d('div', {'className': 'mirosubs-languageList'}); 
    container.appendChild(this.languageList_ = $d('ul', null));
    
    this.subtitlesOff_ = $d('li', null, $d('a', {'href': '#'}, 'Subtitles Off'));
    this.subCountSpan_ = $d('span', 'mirosubs-languageStatus');
    this.originalLanguage_ =
        $d('li', null,
           $d('a', {'href': '#'},
              $d('span', 'mirosubs-languageTitle', 'Original Language'),
              this.subCountSpan_));
    return container;
};

mirosubs.widget.DropDown.prototype.updateSubtitleStats_ = function() {
    var $d = goog.bind(this.getDomHelper().createDom, this.getDomHelper());
    this.getDomHelper().removeChildren(this.languageList_);

    goog.dom.setTextContent(
        this.addTranslationAnchor_,
        this.subtitleCount_ == 0 ? 
            'Add New Subtitles' : 'Add New Translation');

    goog.dom.setTextContent(
        this.subCountSpan_, '(' + this.subtitleCount_ + ' lines)');
    
    this.languageList_.appendChild(
        $d('li', 'mirosubs-hintTranslate',
           $d('span', 'mirosubs-asterisk', '*'),
           ' = Missing sections translated by Google Translate'));
    this.languageList_.appendChild(this.subtitlesOff_);
    if (this.subtitleCount_ > 0)
        this.languageList_.appendChild(this.originalLanguage_);

    this.translationLinks_ = [];
        
    for (var i = 0; i < this.translationLanguages_.length; i++) {
        var link = 
            $d('a', {'href': '#'}, 
               $d('span', 'mirosubs-languageTitle', 
                  mirosubs.languageNameForCode(
                      this.translationLanguages_[i][0])),
               $d('span', 'mirosubs-languageStatus',
                  this.translationLanguages_[i][1] + '%'));
        var linkLi = $d('li', null, link);
        this.translationLinks_.push(
            { link: link, linkLi: linkLi, lang: this.translationLanguages_[i] });
        this.languageList_.appendChild(linkLi);
    }
};

mirosubs.widget.DropDown.prototype.createActionList_ = function($d) {
    this.actions_ = $d('div', {'className': 'mirosubs-actions'});
    this.createActionLinks_($d);
    this.actions_.appendChild(this.unisubsLink_);
    this.actions_.appendChild($d('h4', null, 'THIS VIDEO'));
    this.actions_.appendChild(this.videoActions_);
    this.actions_.appendChild($d('h4', null, 'MY SETTINGS'));
    this.actions_.appendChild(this.settingsActions_);
};

mirosubs.widget.DropDown.prototype.createSubtitleHomepageURL_ = function() {
    return [mirosubs.siteURL(), "/videos/", this.videoID_].join('');
};

mirosubs.widget.DropDown.prototype.createDownloadSRTURL_ = function() {
    var url = [mirosubs.siteURL(),
               "/widget/download_",
               (mirosubs.IS_NULL ? "null_" : ""),
               "srt/?video_id=",
               this.videoID_].join('');
    if (this.subtitleState_ && this.subtitleState_.LANGUAGE)
        url += ['&lang_code=', this.subtitleState_.LANGUAGE].join('');
    return url;
};

mirosubs.widget.DropDown.prototype.createActionLinks_ = function($d) {
    this.videoActions_ = $d('ul', null);    
    this.settingsActions_ = $d('ul', null);

    this.unisubsLink_ = 
        $d('h5', 'mirosubs-uniLogo', 'Universal Subtitles');
    this.addTranslationAnchor_ = 
        $d('a', {'href': '#'}, '');
    this.addTranslationLink_ = 
        $d('li', 'mirosubs-addTranslation', this.addTranslationAnchor_);
    this.improveSubtitlesLink_ = 
        $d('li', 'mirosubs-improveSubtitles',
           $d('a', {'href': '#'}, 'Improve These Subtitles'));
    this.subtitleHomepageLink_ = 
        $d('li', 'mirosubs-subtitleHomepage',
           $d('a', {'href': this.createSubtitleHomepageURL_()},
              'Subtitle Homepage'));
    this.downloadSubtitlesLink_ = 
        $d('li', 'mirosubs-downloadSubtitles',
           $d('a', {'href': this.createDownloadSRTURL_()},
              'Download Subtitles'));
    
    this.createAccountLink_ = 
        $d('li', 'mirosubs-createAccount',
           $d('a', {'href': '#'}, 'Login or Create Account'));
    this.languagePreferencesLink_ = 
        $d('li', 'mirosubs-languagePreferences',
           $d('a', {'href': '#'}, 'Language Preferences'));
    this.usernameLink_ = 
        $d('li', null,
           $d('a', {'href': '#'}, 'USERNAME'));
    this.logoutLink_ = 
        $d('li', null,
           $d('a', {'href': '#'}, 'Logout'));
};

mirosubs.widget.DropDown.prototype.updateActions_ = function() {
    var $d = goog.bind(this.getDomHelper().createDom, this.getDomHelper());
    this.getDomHelper().removeChildren(this.videoActions_);
    this.getDomHelper().removeChildren(this.settingsActions_);

    this.videoActions_.appendChild(this.addTranslationLink_);
    this.videoActions_.appendChild(this.improveSubtitlesLink_);
    this.videoActions_.appendChild(this.subtitleHomepageLink_);
    this.videoActions_.appendChild(this.downloadSubtitlesLink_);
    
    if (mirosubs.currentUsername == null)
        this.settingsActions_.appendChild(this.createAccountLink_);
    else {
        goog.dom.setTextContent(
            goog.dom.getFirstElementChild(this.usernameLink_),
            mirosubs.currentUsername);
        this.settingsActions_.appendChild(this.usernameLink_);
        this.settingsActions_.appendChild(this.logoutLink_);
    }
    this.settingsActions_.appendChild(this.languagePreferencesLink_);
};

mirosubs.widget.DropDown.prototype.enterDocument = function() {
    mirosubs.widget.DropDown.superClass_.enterDocument.call(this);
    var s = mirosubs.widget.DropDown.Selection;
    this.getHandler().
        listen(this.unisubsLink_, 'click',
            function(e) { window.open('http://www.universalsubtitles.org'); }).
        listen(this.addTranslationLink_, 'click',
               goog.bind(this.menuItemClicked_, this, s.ADD_LANGUAGE)).
        listen(this.improveSubtitlesLink_, 'click',
               goog.bind(this.menuItemClicked_, this, s.IMPROVE_SUBTITLES)).
        listen(this.subtitleHomepageLink_, 'click',
               goog.bind(this.menuItemClicked_, this, s.SUBTITLE_HOMEPAGE)).
        listen(this.downloadSubtitlesLink_, 'click',
               goog.bind(this.menuItemClicked_, this, s.DOWNLOAD_SUBTITLES)).
        listen(this.createAccountLink_, 'click',
               goog.bind(this.menuItemClicked_, this, s.CREATE_ACCOUNT)).
        listen(this.languagePreferencesLink_, 'click',
               goog.bind(this.menuItemClicked_, this, s.LANGUAGE_PREFERENCES)).
        listen(this.subtitlesOff_, 'click',
               goog.bind(this.menuItemClicked_, this, s.SUBTITLES_OFF)).
        listen(this.originalLanguage_, 'click',
               goog.bind(this.languageSelected_, this, null)).
        listen(this.usernameLink_, 'click',
               goog.bind(this.menuItemClicked_, this, s.USERNAME)).
        listen(this.logoutLink_, 'click',
               goog.bind(this.menuItemClicked_, this, s.LOGOUT)).
        listen(mirosubs.userEventTarget,
               goog.object.getValues(mirosubs.EventType),
               this.loginStatusChanged).
        listen(this.getDomHelper().getDocument(),
               goog.events.EventType.MOUSEDOWN, 
               this.onDocClick_, true);

    // Webkit doesn't fire a mousedown event when opening the context menu,
    // but we need one to update menu visibility properly. So in Safari handle
    // contextmenu mouse events like mousedown.
    // {@link http://bugs.webkit.org/show_bug.cgi?id=6595}
    if (goog.userAgent.WEBKIT)
        this.getHandler().listen(
            this.getDomHelper().getDocument(),
            goog.events.EventType.CONTEXTMENU, 
            this.onDocClick_, true);
    
    this.addTranslationLinkListeners_();
};

mirosubs.widget.DropDown.prototype.addTranslationLinkListeners_ = function() {
    this.languageClickHandler_.removeAll();
    var that = this;
    goog.array.forEach(this.translationLinks_,
        function(tLink) {
            that.languageClickHandler_.listen(tLink.link, 'click',
                goog.bind(that.languageSelected_, that, tLink.lang[0]));
        });
};

mirosubs.widget.DropDown.prototype.onDocClick_ = function(e) {
    if (this.shown_ &&
        !goog.dom.contains(this.getElement(), e.target) &&
        !goog.dom.contains(this.videoTab_.getElement(), e.target))
        this.hide();
};

mirosubs.widget.DropDown.prototype.menuItemClicked_ = function(type, e) {
    e.preventDefault();

    var s = mirosubs.widget.DropDown.Selection;
    if (type == s.CREATE_ACCOUNT)
        mirosubs.login();
    else if (type == s.LOGOUT)
        mirosubs.logout();
    else if (type == s.USERNAME)
        window.open(mirosubs.siteURL() + '/profiles/mine');
    else if (type == s.LANGUAGE_PREFERENCES)
        window.open(mirosubs.siteURL() + '/profiles/mine');
    else if (type == s.SUBTITLE_HOMEPAGE)
        window.location.replace(goog.dom.getFirstElementChild(this.subtitleHomepageLink_).href);
    else if (type == s.DOWNLOAD_SUBTITLES)
        window.open(goog.dom.getFirstElementChild(this.downloadSubtitlesLink_).href);
    else if (type == s.ADD_LANGUAGE || type == s.IMPROVE_SUBTITLES || type == s.SUBTITLES_OFF)
        this.dispatchEvent(type);
    else
        this.dispatchLanguageSelection_(null);

    this.hide();
};

mirosubs.widget.DropDown.prototype.languageSelected_ = function(langCode, e) {
    e.preventDefault();
    this.dispatchLanguageSelection_(langCode);
};

mirosubs.widget.DropDown.prototype.dispatchLanguageSelection_ = function(langCode) {
    this.dispatchEvent(
        new mirosubs.widget.DropDown.LanguageSelectedEvent(langCode));
};

mirosubs.widget.DropDown.prototype.clearCurrentLang_ = function() {
    this.subtitlesOff_.className = '';
    if (this.originalLanguage_)
        this.originalLanguage_.className = '';
    for (var i = 0; i < this.translationLinks_.length; i++)
        this.translationLinks_[i].linkLi.className = '';
};

mirosubs.widget.DropDown.prototype.setCurrentLangClassName_ = function() {
    var className = 'mirosubs-activeLanguage';
    var that = this;
    if (this.subtitleState_ == null)
        this.subtitlesOff_.className = className;
    else if (!this.subtitleState_.LANGUAGE)
        this.originalLanguage_.className = className;
    else {
        goog.array.find(this.translationLinks_, function(elt) {
            return elt.lang[0] == that.subtitleState_.LANGUAGE;
        }).linkLi.className = className;
    }
};

mirosubs.widget.DropDown.prototype.getTranslationLanguages = function() {
    return this.translationLanguages_;
};

mirosubs.widget.DropDown.prototype.setTranslationLanguages =
    function(translationLanguages) {
    this.translationLanguages_ = translationLanguages;
};

mirosubs.widget.DropDown.prototype.toggleShow = function() {
    if (this.shown_)
        this.hide();
    else
        this.show();
};

mirosubs.widget.DropDown.prototype.hide = function() {
    goog.style.showElement(this.getElement(), false);
    this.shown_ = false;
};

mirosubs.widget.DropDown.prototype.show = function() {
    var anchor = this.videoTab_.getMenuAnchor();
    // This is a little hacky so that we can position the menu with minimal
    // flicker.

    // On IE, setting visibility = 'hidden' on a visible menu
    // will cause a blur, forcing the menu to close immediately.
    this.getElement().style.visibility = 'hidden';

    goog.style.showElement(this.getElement(), true);

    var c = goog.positioning.Corner;
    goog.positioning.positionAtAnchor(
        anchor, c.BOTTOM_LEFT,
        this.getElement(), c.TOP_LEFT);

    this.getElement().style.visibility = 'visible';
    this.shown_ = true;
};

mirosubs.widget.DropDown.prototype.loginStatusChanged = function() {
    this.updateActions_();
};

mirosubs.widget.DropDown.prototype.disposeInternal = function() {
    mirosubs.widget.DropDown.superClass_.disposeInternal.call(this);
    this.languageClickHandler_.dispose();
};

mirosubs.widget.DropDown.LanguageSelectedEvent = function(opt_languageCode) {
    this.type = mirosubs.widget.DropDown.Selection.LANGUAGE_SELECTED;
    /**
     * The language code selected, or null to signify original
     * language.
     * @type {?string}
     */
    this.languageCode = opt_languageCode;
};
