





<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
  <link rel="dns-prefetch" href="https://assets-cdn.github.com">
  <link rel="dns-prefetch" href="https://avatars0.githubusercontent.com">
  <link rel="dns-prefetch" href="https://avatars1.githubusercontent.com">
  <link rel="dns-prefetch" href="https://avatars2.githubusercontent.com">
  <link rel="dns-prefetch" href="https://avatars3.githubusercontent.com">
  <link rel="dns-prefetch" href="https://github-cloud.s3.amazonaws.com">
  <link rel="dns-prefetch" href="https://user-images.githubusercontent.com/">



  <link crossorigin="anonymous" media="all" integrity="sha512-lLo2nlsdl+bHLu6PGvC2j3wfP45RnK4wKQLiPnCDcuXfU38AiD+JCdMywnF3WbJC1jaxe3lAI6AM4uJuMFBLEw==" rel="stylesheet" href="https://assets-cdn.github.com/assets/frameworks-08fc49d3bd2694c870ea23d0906f3610.css" />
  <link crossorigin="anonymous" media="all" integrity="sha512-qkjThICsaVKvzH7MwNjTFb9AcEczVn7R5fUYOjyb5XBUctIsq6vRtyBok0kO4loJxqaNr7wnkWlFuV6rxIexlg==" rel="stylesheet" href="https://assets-cdn.github.com/assets/github-349d95b8186d79601a84c61aea15994d.css" />
  
  
  <link crossorigin="anonymous" media="all" integrity="sha512-PcJMPDRp7jbbEAmTk9kaL2kRQqg69QZ26WsZf07xsPyaipKsi3wVG0805PZNYXxotPDAliKKFvNSQPhD8fp1FQ==" rel="stylesheet" href="https://assets-cdn.github.com/assets/site-50c740d9290419d070dd6213a7cd03b5.css" />
  
  

  <meta name="viewport" content="width=device-width">
  
  <title>server/kaltura_elastic_populate.sh at Naos-14.10.0 · kaltura/server · GitHub</title>
    <meta name="description" content="The Kaltura Platform Backend. To install Kaltura, visit the install packages repository. - kaltura/server">
    <link rel="search" type="application/opensearchdescription+xml" href="/opensearch.xml" title="GitHub">
  <link rel="fluid-icon" href="https://github.com/fluidicon.png" title="GitHub">
  <meta property="fb:app_id" content="1401488693436528">

    
    <meta property="og:image" content="https://avatars0.githubusercontent.com/u/319096?s=400&amp;v=4" /><meta property="og:site_name" content="GitHub" /><meta property="og:type" content="object" /><meta property="og:title" content="kaltura/server" /><meta property="og:url" content="https://github.com/kaltura/server" /><meta property="og:description" content="The Kaltura Platform Backend. To install Kaltura, visit the install packages repository. - kaltura/server" />

  <link rel="assets" href="https://assets-cdn.github.com/">
  
  <meta name="pjax-timeout" content="1000">
  
  <meta name="request-id" content="B6C2:46F10:4E7B193:735F084:5C0FF804" data-pjax-transient>


  

  <meta name="selected-link" value="repo_source" data-pjax-transient>

      <meta name="google-site-verification" content="KT5gs8h0wvaagLKAVWq8bbeNwnZZK1r1XQysX3xurLU">
    <meta name="google-site-verification" content="ZzhVyEFwb7w3e0-uOTltm8Jsck2F5StVihD0exw2fsA">
    <meta name="google-site-verification" content="GXs5KoUUkNCoaAZn7wPN-t01Pywp9M3sEjnt_3_ZWPc">

  <meta name="octolytics-host" content="collector.githubapp.com" /><meta name="octolytics-app-id" content="github" /><meta name="octolytics-event-url" content="https://collector.githubapp.com/github-external/browser_event" /><meta name="octolytics-dimension-request_id" content="B6C2:46F10:4E7B193:735F084:5C0FF804" /><meta name="octolytics-dimension-region_edge" content="ams" /><meta name="octolytics-dimension-region_render" content="iad" />
<meta name="analytics-location" content="/&lt;user-name&gt;/&lt;repo-name&gt;/blob/show" data-pjax-transient="true" />



    <meta name="google-analytics" content="UA-3769691-2">


<meta class="js-ga-set" name="dimension1" content="Logged Out">



  

      <meta name="hostname" content="github.com">
    <meta name="user-login" content="">

      <meta name="expected-hostname" content="github.com">
    <meta name="js-proxy-site-detection-payload" content="ZDIwNjM5MWFlNDU0MTM3MWZlM2Y5MDhjNDgxNjNmZWE4NDI5YzMwMTFkNWMwZTczZjk3MmQ0NmNlMjk5MzU0Mnx7InJlbW90ZV9hZGRyZXNzIjoiMTg4LjMwLjEwMy4yMDkiLCJyZXF1ZXN0X2lkIjoiQjZDMjo0NkYxMDo0RTdCMTkzOjczNUYwODQ6NUMwRkY4MDQiLCJ0aW1lc3RhbXAiOjE1NDQ1NTA0MDQsImhvc3QiOiJnaXRodWIuY29tIn0=">

    <meta name="enabled-features" content="DASHBOARD_V2_LAYOUT_OPT_IN,EXPLORE_DISCOVER_REPOSITORIES,UNIVERSE_BANNER,MARKETPLACE_PLAN_RESTRICTION_EDITOR">

  <meta name="html-safe-nonce" content="59267a5778f0c538979fb56d2045ebbb556eb55e">

  <meta http-equiv="x-pjax-version" content="db126e88dcbd892b05eaf334d819421c">
  

      <link href="https://github.com/kaltura/server/commits/Naos-14.10.0.atom" rel="alternate" title="Recent Commits to server:Naos-14.10.0" type="application/atom+xml">

  <meta name="go-import" content="github.com/kaltura/server git https://github.com/kaltura/server.git">

  <meta name="octolytics-dimension-user_id" content="319096" /><meta name="octolytics-dimension-user_login" content="kaltura" /><meta name="octolytics-dimension-repository_id" content="11364783" /><meta name="octolytics-dimension-repository_nwo" content="kaltura/server" /><meta name="octolytics-dimension-repository_public" content="true" /><meta name="octolytics-dimension-repository_is_fork" content="false" /><meta name="octolytics-dimension-repository_network_root_id" content="11364783" /><meta name="octolytics-dimension-repository_network_root_nwo" content="kaltura/server" /><meta name="octolytics-dimension-repository_explore_github_marketplace_ci_cta_shown" content="false" />


    <link rel="canonical" href="https://github.com/kaltura/server/blob/Naos-14.10.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh" data-pjax-transient>


  <meta name="browser-stats-url" content="https://api.github.com/_private/browser/stats">

  <meta name="browser-errors-url" content="https://api.github.com/_private/browser/errors">

  <link rel="mask-icon" href="https://assets-cdn.github.com/pinned-octocat.svg" color="#000000">
  <link rel="icon" type="image/x-icon" class="js-site-favicon" href="https://assets-cdn.github.com/favicon.ico">

<meta name="theme-color" content="#1e2327">



  <link rel="manifest" href="/manifest.json" crossOrigin="use-credentials">

  </head>

  <body class="logged-out env-production page-blob">
    

  <div class="position-relative js-header-wrapper ">
    <a href="#start-of-content" tabindex="1" class="px-2 py-4 bg-blue text-white show-on-focus js-skip-to-content">Skip to content</a>
    <div id="js-pjax-loader-bar" class="pjax-loader-bar"><div class="progress"></div></div>

    
    
    


        
<header class="Header header-logged-out  position-relative f4 py-3" role="banner">
  <div class="container-lg d-flex px-3">
    <div class="d-flex flex-justify-between flex-items-center">
        <a class="mr-4" href="https://github.com/" aria-label="Homepage" data-ga-click="(Logged out) Header, go to homepage, icon:logo-wordmark; experiment:site_header_dropdowns; group:dropdowns">
          <svg height="32" class="octicon octicon-mark-github text-white" viewBox="0 0 16 16" version="1.1" width="32" aria-hidden="true"><path fill-rule="evenodd" d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0 0 16 8c0-4.42-3.58-8-8-8z"/></svg>
        </a>
    </div>

    <div class="HeaderMenu HeaderMenu--logged-out d-flex flex-justify-between flex-items-center flex-auto">
      <div class="d-none">
        <button class="btn-link js-details-target" type="button" aria-label="Toggle navigation" aria-expanded="false">
          <svg height="24" class="octicon octicon-x text-gray" viewBox="0 0 12 16" version="1.1" width="18" aria-hidden="true"><path fill-rule="evenodd" d="M7.48 8l3.75 3.75-1.48 1.48L6 9.48l-3.75 3.75-1.48-1.48L4.52 8 .77 4.25l1.48-1.48L6 6.52l3.75-3.75 1.48 1.48L7.48 8z"/></svg>
        </button>
      </div>

        <nav class="mt-0" aria-label="Global">
          <ul class="d-flex list-style-none">
              <li class=" mr-3 mr-lg-3 edge-item-fix position-relative flex-wrap flex-justify-between d-flex flex-items-center ">
                <details class="HeaderMenu-details details-overlay details-reset width-full">
                  <summary class="HeaderMenu-summary HeaderMenu-link px-0 py-3 border-0 no-wrap  d-inline-block">
                    Why GitHub?
                    <svg x="0px" y="0px" viewBox="0 0 14 8" xml:space="preserve" fill="none" class="icon-chevon-down-mktg position-relative">
                      <path d="M1,1l6.2,6L13,1"></path>
                    </svg>
                  </summary>
                  <div class="dropdown-menu flex-auto rounded-1 bg-white px-0 mt-0  p-4 left-n4 position-absolute">
                    <a href="/features" class="py-2 lh-condensed-ultra d-block link-gray-dark no-underline h5 Bump-link--hover" data-ga-click="(Logged out) Header, go to Features">Features <span class="Bump-link-symbol float-right text-normal text-gray-light">&rarr;</span></a>
                    <ul class="list-style-none f5 pb-3">
                      <li class="edge-item-fix"><a href="/features/code-review/" class="py-2 lh-condensed-ultra d-block link-gray no-underline f5" data-ga-click="(Logged out) Header, go to Code review">Code review</a></li>
                      <li class="edge-item-fix"><a href="/features/project-management/" class="py-2 lh-condensed-ultra d-block link-gray no-underline f5" data-ga-click="(Logged out) Header, go to Project management">Project management</a></li>
                      <li class="edge-item-fix"><a href="/features/integrations" class="py-2 lh-condensed-ultra d-block link-gray no-underline f5" data-ga-click="(Logged out) Header, go to Integrations">Integrations</a></li>
                      <li class="edge-item-fix"><a href="/features#team-management" class="py-2 lh-condensed-ultra d-block link-gray no-underline f5" data-ga-click="(Logged out) Header, go to Team management">Team management</a></li>
                      <li class="edge-item-fix"><a href="/features#social-coding" class="py-2 lh-condensed-ultra d-block link-gray no-underline f5" data-ga-click="(Logged out) Header, go to Social coding">Social coding</a></li>
                      <li class="edge-item-fix"><a href="/features#documentation" class="py-2 lh-condensed-ultra d-block link-gray no-underline f5" data-ga-click="(Logged out) Header, go to Documentation">Documentation</a></li>
                      <li class="edge-item-fix"><a href="/features#code-hosting" class="py-2 lh-condensed-ultra d-block link-gray no-underline f5" data-ga-click="(Logged out) Header, go to Code hosting">Code hosting</a></li>
                    </ul>

                    <ul class="list-style-none mb-0 border-lg-top pt-lg-3">
                      <li class="edge-item-fix"><a href="/case-studies" class="py-2 lh-condensed-ultra d-block no-underline link-gray-dark no-underline h5 Bump-link--hover" data-ga-click="(Logged out) Header, go to Case studies">Case Studies <span class="Bump-link-symbol float-right text-normal text-gray-light">&rarr;</span></a></li>
                      <li class="edge-item-fix"><a href="/security" class="py-2 lh-condensed-ultra d-block no-underline link-gray-dark no-underline h5 Bump-link--hover" data-ga-click="(Logged out) Header, go to Security">Security <span class="Bump-link-symbol float-right text-normal text-gray-light">&rarr;</span></a></li>
                    </ul>
                  </div>
                </details>
              </li>
              <li class=" mr-3 mr-lg-3">
                <a href="/business" class="HeaderMenu-link no-underline py-3 d-block d-lg-inline-block" data-ga-click="(Logged out) Header, go to Business">Business</a>
              </li>

              <li class=" mr-3 mr-lg-3 edge-item-fix position-relative flex-wrap flex-justify-between d-flex flex-items-center ">
                <details class="HeaderMenu-details details-overlay details-reset width-full">
                  <summary class="HeaderMenu-summary HeaderMenu-link px-0 py-3 border-0 no-wrap  d-inline-block">
                    Explore
                    <svg x="0px" y="0px" viewBox="0 0 14 8" xml:space="preserve" fill="none" class="icon-chevon-down-mktg position-relative">
                      <path d="M1,1l6.2,6L13,1"></path>
                    </svg>
                  </summary>

                  <div class="dropdown-menu flex-auto rounded-1 bg-white px-0 pt-2 pb-0 mt-0  p-4 left-n4 position-absolute">
                    <ul class="list-style-none mb-3">
                      <li class="edge-item-fix"><a href="/explore" class="py-2 lh-condensed-ultra d-block link-gray-dark no-underline h5 Bump-link--hover" data-ga-click="(Logged out) Header, go to Features">Explore GitHub <span class="Bump-link-symbol float-right text-normal text-gray-light">&rarr;</span></a></li>
                    </ul>

                    <h4 class="text-gray-light text-normal text-mono f5 mb-2  border-top pt-3">Learn &amp; contribute</h4>
                    <ul class="list-style-none mb-3">
                      <li class="edge-item-fix"><a href="/topics" class="py-2 lh-condensed-ultra d-block link-gray no-underline f5" data-ga-click="(Logged out) Header, go to Topics">Topics</a></li>
                      <li class="edge-item-fix"><a href="/collections" class="py-2 lh-condensed-ultra d-block link-gray no-underline f5" data-ga-click="(Logged out) Header, go to Collections">Collections</a></li>
                      <li class="edge-item-fix"><a href="/trending" class="py-2 lh-condensed-ultra d-block link-gray no-underline f5" data-ga-click="(Logged out) Header, go to Trending">Trending</a></li>
                      <li class="edge-item-fix"><a href="https://lab.github.com/" class="py-2 lh-condensed-ultra d-block link-gray no-underline f5" data-ga-click="(Logged out) Header, go to Learning lab">Learning Lab</a></li>
                      <li class="edge-item-fix"><a href="https://opensource.guide" class="py-2 lh-condensed-ultra d-block link-gray no-underline f5" data-ga-click="(Logged out) Header, go to Open source guides">Open source guides</a></li>
                    </ul>

                    <h4 class="text-gray-light text-normal text-mono f5 mb-2  border-top pt-3">Connect with others</h4>
                    <ul class="list-style-none mb-0">
                      <li class="edge-item-fix"><a href="/events" class="py-2 lh-condensed-ultra d-block link-gray no-underline f5" data-ga-click="(Logged out) Header, go to Events">Events</a></li>
                      <li class="edge-item-fix"><a href="https://github.community" class="py-2 lh-condensed-ultra d-block link-gray no-underline f5" data-ga-click="(Logged out) Header, go to Community forum">Community forum</a></li>
                      <li class="edge-item-fix"><a href="https://education.github.com" class="py-2 pb-0 lh-condensed-ultra d-block link-gray no-underline f5" data-ga-click="(Logged out) Header, go to GitHub Education">GitHub Education</a></li>
                    </ul>
                  </div>
                </details>
              </li>

              <li class=" mr-3 mr-lg-3">
                <a href="/marketplace" class="HeaderMenu-link no-underline py-3 d-block d-lg-inline-block" data-ga-click="(Logged out) Header, go to Marketplace">Marketplace</a>
              </li>

              <li class=" mr-3 mr-lg-3 edge-item-fix position-relative flex-wrap flex-justify-between d-flex flex-items-center ">
                <details class="HeaderMenu-details details-overlay details-reset width-full">
                  <summary class="HeaderMenu-summary HeaderMenu-link px-0 py-3 border-0 no-wrap  d-inline-block">
                    Pricing
                    <svg x="0px" y="0px" viewBox="0 0 14 8" xml:space="preserve" fill="none" class="icon-chevon-down-mktg position-relative">
                       <path d="M1,1l6.2,6L13,1"></path>
                    </svg>
                  </summary>

                  <div class="dropdown-menu flex-auto rounded-1 bg-white px-0 pt-2 pb-4 mt-0  p-4 left-n4 position-absolute">
                    <a href="/pricing" class="pb-2 lh-condensed-ultra d-block link-gray-dark no-underline h5 Bump-link--hover" data-ga-click="(Logged out) Header, go to Pricing">Plans <span class="Bump-link-symbol float-right text-normal text-gray-light">&rarr;</span></a>
                    <ul class="list-style-none mb-3">
                      <li class="edge-item-fix"><a href="/pricing/developer" class="py-2 lh-condensed-ultra d-block link-gray no-underline f5" data-ga-click="(Logged out) Header, go to Developers">Developer</a></li>
                      <li class="edge-item-fix"><a href="/pricing/team" class="py-2 lh-condensed-ultra d-block link-gray no-underline f5" data-ga-click="(Logged out) Header, go to Team">Team</a></li>
                      <li class="edge-item-fix"><a href="/pricing/business-cloud" class="py-2 lh-condensed-ultra d-block link-gray no-underline f5" data-ga-click="(Logged out) Header, go to Business Cloud">Business Cloud</a></li>
                      <li class="edge-item-fix"><a href="/pricing/enterprise" class="py-2 lh-condensed-ultra d-block link-gray no-underline f5" data-ga-click="(Logged out) Header, go to Enterprise">Enterprise</a></li>
                    </ul>

                    <ul class="list-style-none mb-0  border-top pt-3">
                      <li class="edge-item-fix"><a href="/pricing#feature-comparison" class="py-2 lh-condensed-ultra d-block no-underline link-gray-dark no-underline h5 Bump-link--hover" data-ga-click="(Logged out) Header, go to Compare features">Compare plans <span class="Bump-link-symbol float-right text-normal text-gray-light">&rarr;</span></a></li>
                      <li class="edge-item-fix"><a href="/nonprofit" class="py-2 lh-condensed-ultra d-block no-underline link-gray-dark no-underline h5 Bump-link--hover" data-ga-click="(Logged out) Header, go to Nonprofits">Nonprofit <span class="Bump-link-symbol float-right text-normal text-gray-light">&rarr;</span></a></li>
                      <li class="edge-item-fix"><a href="https://education.github.com/discount_requests/new" class="py-2 pb-0 lh-condensed-ultra d-block no-underline link-gray-dark no-underline h5 Bump-link--hover"  data-ga-click="(Logged out) Header, go to Education">Education <span class="Bump-link-symbol float-right text-normal text-gray-light">&rarr;</span></a></li>
                    </ul>
                  </div>
                </details>
              </li>
          </ul>
        </nav>

      <div class="d-flex flex-items-center px-0 text-center text-left">
          <div class="d-lg-flex mr-3">
            <div class="header-search scoped-search site-scoped-search js-site-search position-relative js-jump-to"
  role="combobox"
  aria-owns="jump-to-results"
  aria-label="Search or jump to"
  aria-haspopup="listbox"
  aria-expanded="false"
>
  <div class="position-relative">
    <!-- '"` --><!-- </textarea></xmp> --></option></form><form class="js-site-search-form" data-scope-type="Repository" data-scope-id="11364783" data-scoped-search-url="/kaltura/server/search" data-unscoped-search-url="/search" action="/kaltura/server/search" accept-charset="UTF-8" method="get"><input name="utf8" type="hidden" value="&#x2713;" />
      <label class="form-control header-search-wrapper header-search-wrapper-jump-to position-relative d-flex flex-justify-between flex-items-center js-chromeless-input-container">
        <input type="text"
          class="form-control header-search-input jump-to-field js-jump-to-field js-site-search-focus js-site-search-field is-clearable"
          data-hotkey="s,/"
          name="q"
          value=""
          placeholder="Search"
          data-unscoped-placeholder="Search GitHub"
          data-scoped-placeholder="Search"
          autocapitalize="off"
          aria-autocomplete="list"
          aria-controls="jump-to-results"
          aria-label="Search"
          data-jump-to-suggestions-path="/_graphql/GetSuggestedNavigationDestinations#csrf-token=ZmWgcuklP6cJ551oZZeJFnvt35BBV/8ewRGadhPKKDFR6BK8SwoEvs7wVAedVjXIeSPywNJWuq2hPSdusEnBIg=="
          spellcheck="false"
          autocomplete="off"
          >
          <input type="hidden" class="js-site-search-type-field" name="type" >
            <img src="https://assets-cdn.github.com/images/search-key-slash.svg" alt="" class="mr-2 header-search-key-slash">

            <div class="Box position-absolute overflow-hidden d-none jump-to-suggestions js-jump-to-suggestions-container">
              
<ul class="d-none js-jump-to-suggestions-template-container">
  

<li class="d-flex flex-justify-start flex-items-center p-0 f5 navigation-item js-navigation-item js-jump-to-suggestion" role="option">
  <a tabindex="-1" class="no-underline d-flex flex-auto flex-items-center jump-to-suggestions-path js-jump-to-suggestion-path js-navigation-open p-2" href="">
    <div class="jump-to-octicon js-jump-to-octicon flex-shrink-0 mr-2 text-center d-none">
      <svg height="16" width="16" class="octicon octicon-repo flex-shrink-0 js-jump-to-octicon-repo d-none" title="Repository" aria-label="Repository" viewBox="0 0 12 16" version="1.1" role="img"><path fill-rule="evenodd" d="M4 9H3V8h1v1zm0-3H3v1h1V6zm0-2H3v1h1V4zm0-2H3v1h1V2zm8-1v12c0 .55-.45 1-1 1H6v2l-1.5-1.5L3 16v-2H1c-.55 0-1-.45-1-1V1c0-.55.45-1 1-1h10c.55 0 1 .45 1 1zm-1 10H1v2h2v-1h3v1h5v-2zm0-10H2v9h9V1z"/></svg>
      <svg height="16" width="16" class="octicon octicon-project flex-shrink-0 js-jump-to-octicon-project d-none" title="Project" aria-label="Project" viewBox="0 0 15 16" version="1.1" role="img"><path fill-rule="evenodd" d="M10 12h3V2h-3v10zm-4-2h3V2H6v8zm-4 4h3V2H2v12zm-1 1h13V1H1v14zM14 0H1a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h13a1 1 0 0 0 1-1V1a1 1 0 0 0-1-1z"/></svg>
      <svg height="16" width="16" class="octicon octicon-search flex-shrink-0 js-jump-to-octicon-search d-none" title="Search" aria-label="Search" viewBox="0 0 16 16" version="1.1" role="img"><path fill-rule="evenodd" d="M15.7 13.3l-3.81-3.83A5.93 5.93 0 0 0 13 6c0-3.31-2.69-6-6-6S1 2.69 1 6s2.69 6 6 6c1.3 0 2.48-.41 3.47-1.11l3.83 3.81c.19.2.45.3.7.3.25 0 .52-.09.7-.3a.996.996 0 0 0 0-1.41v.01zM7 10.7c-2.59 0-4.7-2.11-4.7-4.7 0-2.59 2.11-4.7 4.7-4.7 2.59 0 4.7 2.11 4.7 4.7 0 2.59-2.11 4.7-4.7 4.7z"/></svg>
    </div>

    <img class="avatar mr-2 flex-shrink-0 js-jump-to-suggestion-avatar d-none" alt="" aria-label="Team" src="" width="28" height="28">

    <div class="jump-to-suggestion-name js-jump-to-suggestion-name flex-auto overflow-hidden text-left no-wrap css-truncate css-truncate-target">
    </div>

    <div class="border rounded-1 flex-shrink-0 bg-gray px-1 text-gray-light ml-1 f6 d-none js-jump-to-badge-search">
      <span class="js-jump-to-badge-search-text-default d-none" aria-label="in this repository">
        In this repository
      </span>
      <span class="js-jump-to-badge-search-text-global d-none" aria-label="in all of GitHub">
        All GitHub
      </span>
      <span aria-hidden="true" class="d-inline-block ml-1 v-align-middle">↵</span>
    </div>

    <div aria-hidden="true" class="border rounded-1 flex-shrink-0 bg-gray px-1 text-gray-light ml-1 f6 d-none d-on-nav-focus js-jump-to-badge-jump">
      Jump to
      <span class="d-inline-block ml-1 v-align-middle">↵</span>
    </div>
  </a>
</li>

</ul>

<ul class="d-none js-jump-to-no-results-template-container">
  <li class="d-flex flex-justify-center flex-items-center f5 d-none js-jump-to-suggestion p-2">
    <span class="text-gray">No suggested jump to results</span>
  </li>
</ul>

<ul id="jump-to-results" role="listbox" class="p-0 m-0 js-navigation-container jump-to-suggestions-results-container js-jump-to-suggestions-results-container">
  

<li class="d-flex flex-justify-start flex-items-center p-0 f5 navigation-item js-navigation-item js-jump-to-scoped-search d-none" role="option">
  <a tabindex="-1" class="no-underline d-flex flex-auto flex-items-center jump-to-suggestions-path js-jump-to-suggestion-path js-navigation-open p-2" href="">
    <div class="jump-to-octicon js-jump-to-octicon flex-shrink-0 mr-2 text-center d-none">
      <svg height="16" width="16" class="octicon octicon-repo flex-shrink-0 js-jump-to-octicon-repo d-none" title="Repository" aria-label="Repository" viewBox="0 0 12 16" version="1.1" role="img"><path fill-rule="evenodd" d="M4 9H3V8h1v1zm0-3H3v1h1V6zm0-2H3v1h1V4zm0-2H3v1h1V2zm8-1v12c0 .55-.45 1-1 1H6v2l-1.5-1.5L3 16v-2H1c-.55 0-1-.45-1-1V1c0-.55.45-1 1-1h10c.55 0 1 .45 1 1zm-1 10H1v2h2v-1h3v1h5v-2zm0-10H2v9h9V1z"/></svg>
      <svg height="16" width="16" class="octicon octicon-project flex-shrink-0 js-jump-to-octicon-project d-none" title="Project" aria-label="Project" viewBox="0 0 15 16" version="1.1" role="img"><path fill-rule="evenodd" d="M10 12h3V2h-3v10zm-4-2h3V2H6v8zm-4 4h3V2H2v12zm-1 1h13V1H1v14zM14 0H1a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h13a1 1 0 0 0 1-1V1a1 1 0 0 0-1-1z"/></svg>
      <svg height="16" width="16" class="octicon octicon-search flex-shrink-0 js-jump-to-octicon-search d-none" title="Search" aria-label="Search" viewBox="0 0 16 16" version="1.1" role="img"><path fill-rule="evenodd" d="M15.7 13.3l-3.81-3.83A5.93 5.93 0 0 0 13 6c0-3.31-2.69-6-6-6S1 2.69 1 6s2.69 6 6 6c1.3 0 2.48-.41 3.47-1.11l3.83 3.81c.19.2.45.3.7.3.25 0 .52-.09.7-.3a.996.996 0 0 0 0-1.41v.01zM7 10.7c-2.59 0-4.7-2.11-4.7-4.7 0-2.59 2.11-4.7 4.7-4.7 2.59 0 4.7 2.11 4.7 4.7 0 2.59-2.11 4.7-4.7 4.7z"/></svg>
    </div>

    <img class="avatar mr-2 flex-shrink-0 js-jump-to-suggestion-avatar d-none" alt="" aria-label="Team" src="" width="28" height="28">

    <div class="jump-to-suggestion-name js-jump-to-suggestion-name flex-auto overflow-hidden text-left no-wrap css-truncate css-truncate-target">
    </div>

    <div class="border rounded-1 flex-shrink-0 bg-gray px-1 text-gray-light ml-1 f6 d-none js-jump-to-badge-search">
      <span class="js-jump-to-badge-search-text-default d-none" aria-label="in this repository">
        In this repository
      </span>
      <span class="js-jump-to-badge-search-text-global d-none" aria-label="in all of GitHub">
        All GitHub
      </span>
      <span aria-hidden="true" class="d-inline-block ml-1 v-align-middle">↵</span>
    </div>

    <div aria-hidden="true" class="border rounded-1 flex-shrink-0 bg-gray px-1 text-gray-light ml-1 f6 d-none d-on-nav-focus js-jump-to-badge-jump">
      Jump to
      <span class="d-inline-block ml-1 v-align-middle">↵</span>
    </div>
  </a>
</li>

  

<li class="d-flex flex-justify-start flex-items-center p-0 f5 navigation-item js-navigation-item js-jump-to-global-search d-none" role="option">
  <a tabindex="-1" class="no-underline d-flex flex-auto flex-items-center jump-to-suggestions-path js-jump-to-suggestion-path js-navigation-open p-2" href="">
    <div class="jump-to-octicon js-jump-to-octicon flex-shrink-0 mr-2 text-center d-none">
      <svg height="16" width="16" class="octicon octicon-repo flex-shrink-0 js-jump-to-octicon-repo d-none" title="Repository" aria-label="Repository" viewBox="0 0 12 16" version="1.1" role="img"><path fill-rule="evenodd" d="M4 9H3V8h1v1zm0-3H3v1h1V6zm0-2H3v1h1V4zm0-2H3v1h1V2zm8-1v12c0 .55-.45 1-1 1H6v2l-1.5-1.5L3 16v-2H1c-.55 0-1-.45-1-1V1c0-.55.45-1 1-1h10c.55 0 1 .45 1 1zm-1 10H1v2h2v-1h3v1h5v-2zm0-10H2v9h9V1z"/></svg>
      <svg height="16" width="16" class="octicon octicon-project flex-shrink-0 js-jump-to-octicon-project d-none" title="Project" aria-label="Project" viewBox="0 0 15 16" version="1.1" role="img"><path fill-rule="evenodd" d="M10 12h3V2h-3v10zm-4-2h3V2H6v8zm-4 4h3V2H2v12zm-1 1h13V1H1v14zM14 0H1a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h13a1 1 0 0 0 1-1V1a1 1 0 0 0-1-1z"/></svg>
      <svg height="16" width="16" class="octicon octicon-search flex-shrink-0 js-jump-to-octicon-search d-none" title="Search" aria-label="Search" viewBox="0 0 16 16" version="1.1" role="img"><path fill-rule="evenodd" d="M15.7 13.3l-3.81-3.83A5.93 5.93 0 0 0 13 6c0-3.31-2.69-6-6-6S1 2.69 1 6s2.69 6 6 6c1.3 0 2.48-.41 3.47-1.11l3.83 3.81c.19.2.45.3.7.3.25 0 .52-.09.7-.3a.996.996 0 0 0 0-1.41v.01zM7 10.7c-2.59 0-4.7-2.11-4.7-4.7 0-2.59 2.11-4.7 4.7-4.7 2.59 0 4.7 2.11 4.7 4.7 0 2.59-2.11 4.7-4.7 4.7z"/></svg>
    </div>

    <img class="avatar mr-2 flex-shrink-0 js-jump-to-suggestion-avatar d-none" alt="" aria-label="Team" src="" width="28" height="28">

    <div class="jump-to-suggestion-name js-jump-to-suggestion-name flex-auto overflow-hidden text-left no-wrap css-truncate css-truncate-target">
    </div>

    <div class="border rounded-1 flex-shrink-0 bg-gray px-1 text-gray-light ml-1 f6 d-none js-jump-to-badge-search">
      <span class="js-jump-to-badge-search-text-default d-none" aria-label="in this repository">
        In this repository
      </span>
      <span class="js-jump-to-badge-search-text-global d-none" aria-label="in all of GitHub">
        All GitHub
      </span>
      <span aria-hidden="true" class="d-inline-block ml-1 v-align-middle">↵</span>
    </div>

    <div aria-hidden="true" class="border rounded-1 flex-shrink-0 bg-gray px-1 text-gray-light ml-1 f6 d-none d-on-nav-focus js-jump-to-badge-jump">
      Jump to
      <span class="d-inline-block ml-1 v-align-middle">↵</span>
    </div>
  </a>
</li>


</ul>

            </div>
      </label>
</form>  </div>
</div>

          </div>

        <a class="HeaderMenu-link no-underline mr-3" href="/login?return_to=%2Fkaltura%2Fserver%2Fblob%2FNaos-14.10.0%2Fplugins%2Fsearch%2Fproviders%2Felastic_search%2Fscripts%2Fkaltura_elastic_populate.sh" data-ga-click="(Logged out) Header, clicked Sign in, text:sign-in">Sign&nbsp;in</a>
          <a class="HeaderMenu-link d-inline-block no-underline border border-gray-dark rounded-1 px-2 py-1" href="/join" data-ga-click="(Logged out) Header, clicked Sign up, text:sign-up">Sign&nbsp;up</a>
      </div>
    </div>
  </div>
</header>

  </div>

  <div id="start-of-content" class="show-on-focus"></div>

    <div id="js-flash-container">

</div>



  <div role="main" class="application-main " data-commit-hovercards-enabled>
        <div itemscope itemtype="http://schema.org/SoftwareSourceCode" class="">
    <div id="js-repo-pjax-container" data-pjax-container >
      


  


  



  <div class="pagehead repohead instapaper_ignore readability-menu experiment-repo-nav  ">
    <div class="repohead-details-container clearfix container">

      <ul class="pagehead-actions">
  <li>
      <a href="/login?return_to=%2Fkaltura%2Fserver"
    class="btn btn-sm btn-with-count tooltipped tooltipped-s"
    aria-label="You must be signed in to watch a repository" rel="nofollow">
    <svg class="octicon octicon-eye v-align-text-bottom" viewBox="0 0 16 16" version="1.1" width="16" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M8.06 2C3 2 0 8 0 8s3 6 8.06 6C13 14 16 8 16 8s-3-6-7.94-6zM8 12c-2.2 0-4-1.78-4-4 0-2.2 1.8-4 4-4 2.22 0 4 1.8 4 4 0 2.22-1.78 4-4 4zm2-4c0 1.11-.89 2-2 2-1.11 0-2-.89-2-2 0-1.11.89-2 2-2 1.11 0 2 .89 2 2z"/></svg>
    Watch
  </a>
  <a class="social-count" href="/kaltura/server/watchers"
     aria-label="83 users are watching this repository">
    83
  </a>

  </li>

  <li>
      <a href="/login?return_to=%2Fkaltura%2Fserver"
    class="btn btn-sm btn-with-count tooltipped tooltipped-s"
    aria-label="You must be signed in to star a repository" rel="nofollow">
    <svg class="octicon octicon-star v-align-text-bottom" viewBox="0 0 14 16" version="1.1" width="14" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M14 6l-4.9-.64L7 1 4.9 5.36 0 6l3.6 3.26L2.67 14 7 11.67 11.33 14l-.93-4.74L14 6z"/></svg>
    Star
  </a>

    <a class="social-count js-social-count" href="/kaltura/server/stargazers"
      aria-label="203 users starred this repository">
      203
    </a>

  </li>

  <li>
      <a href="/login?return_to=%2Fkaltura%2Fserver"
        class="btn btn-sm btn-with-count tooltipped tooltipped-s"
        aria-label="You must be signed in to fork a repository" rel="nofollow">
        <svg class="octicon octicon-repo-forked v-align-text-bottom" viewBox="0 0 10 16" version="1.1" width="10" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M8 1a1.993 1.993 0 0 0-1 3.72V6L5 8 3 6V4.72A1.993 1.993 0 0 0 2 1a1.993 1.993 0 0 0-1 3.72V6.5l3 3v1.78A1.993 1.993 0 0 0 5 15a1.993 1.993 0 0 0 1-3.72V9.5l3-3V4.72A1.993 1.993 0 0 0 8 1zM2 4.2C1.34 4.2.8 3.65.8 3c0-.65.55-1.2 1.2-1.2.65 0 1.2.55 1.2 1.2 0 .65-.55 1.2-1.2 1.2zm3 10c-.66 0-1.2-.55-1.2-1.2 0-.65.55-1.2 1.2-1.2.65 0 1.2.55 1.2 1.2 0 .65-.55 1.2-1.2 1.2zm3-10c-.66 0-1.2-.55-1.2-1.2 0-.65.55-1.2 1.2-1.2.65 0 1.2.55 1.2 1.2 0 .65-.55 1.2-1.2 1.2z"/></svg>
        Fork
      </a>

    <a href="/kaltura/server/network/members" class="social-count"
       aria-label="133 users forked this repository">
      133
    </a>
  </li>
</ul>

      <h1 class="public ">
  <svg class="octicon octicon-repo" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M4 9H3V8h1v1zm0-3H3v1h1V6zm0-2H3v1h1V4zm0-2H3v1h1V2zm8-1v12c0 .55-.45 1-1 1H6v2l-1.5-1.5L3 16v-2H1c-.55 0-1-.45-1-1V1c0-.55.45-1 1-1h10c.55 0 1 .45 1 1zm-1 10H1v2h2v-1h3v1h5v-2zm0-10H2v9h9V1z"/></svg>
  <span class="author" itemprop="author"><a class="url fn" rel="author" data-hovercard-type="organization" data-hovercard-url="/orgs/kaltura/hovercard" href="/kaltura">kaltura</a></span><!--
--><span class="path-divider">/</span><!--
--><strong itemprop="name"><a data-pjax="#js-repo-pjax-container" href="/kaltura/server">server</a></strong>

</h1>

    </div>
    
<nav class="reponav js-repo-nav js-sidenav-container-pjax container"
     itemscope
     itemtype="http://schema.org/BreadcrumbList"
    aria-label="Repository"
     data-pjax="#js-repo-pjax-container">

  <span itemscope itemtype="http://schema.org/ListItem" itemprop="itemListElement">
    <a class="js-selected-navigation-item selected reponav-item" itemprop="url" data-hotkey="g c" aria-current="page" data-selected-links="repo_source repo_downloads repo_commits repo_releases repo_tags repo_branches repo_packages /kaltura/server" href="/kaltura/server">
      <svg class="octicon octicon-code" viewBox="0 0 14 16" version="1.1" width="14" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M9.5 3L8 4.5 11.5 8 8 11.5 9.5 13 14 8 9.5 3zm-5 0L0 8l4.5 5L6 11.5 2.5 8 6 4.5 4.5 3z"/></svg>
      <span itemprop="name">Code</span>
      <meta itemprop="position" content="1">
</a>  </span>

    <span itemscope itemtype="http://schema.org/ListItem" itemprop="itemListElement">
      <a itemprop="url" data-hotkey="g i" class="js-selected-navigation-item reponav-item" data-selected-links="repo_issues repo_labels repo_milestones /kaltura/server/issues" href="/kaltura/server/issues">
        <svg class="octicon octicon-issue-opened" viewBox="0 0 14 16" version="1.1" width="14" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M7 2.3c3.14 0 5.7 2.56 5.7 5.7s-2.56 5.7-5.7 5.7A5.71 5.71 0 0 1 1.3 8c0-3.14 2.56-5.7 5.7-5.7zM7 1C3.14 1 0 4.14 0 8s3.14 7 7 7 7-3.14 7-7-3.14-7-7-7zm1 3H6v5h2V4zm0 6H6v2h2v-2z"/></svg>
        <span itemprop="name">Issues</span>
        <span class="Counter">21</span>
        <meta itemprop="position" content="2">
</a>    </span>

  <span itemscope itemtype="http://schema.org/ListItem" itemprop="itemListElement">
    <a data-hotkey="g p" itemprop="url" class="js-selected-navigation-item reponav-item" data-selected-links="repo_pulls checks /kaltura/server/pulls" href="/kaltura/server/pulls">
      <svg class="octicon octicon-git-pull-request" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M11 11.28V5c-.03-.78-.34-1.47-.94-2.06C9.46 2.35 8.78 2.03 8 2H7V0L4 3l3 3V4h1c.27.02.48.11.69.31.21.2.3.42.31.69v6.28A1.993 1.993 0 0 0 10 15a1.993 1.993 0 0 0 1-3.72zm-1 2.92c-.66 0-1.2-.55-1.2-1.2 0-.65.55-1.2 1.2-1.2.65 0 1.2.55 1.2 1.2 0 .65-.55 1.2-1.2 1.2zM4 3c0-1.11-.89-2-2-2a1.993 1.993 0 0 0-1 3.72v6.56A1.993 1.993 0 0 0 2 15a1.993 1.993 0 0 0 1-3.72V4.72c.59-.34 1-.98 1-1.72zm-.8 10c0 .66-.55 1.2-1.2 1.2-.65 0-1.2-.55-1.2-1.2 0-.65.55-1.2 1.2-1.2.65 0 1.2.55 1.2 1.2zM2 4.2C1.34 4.2.8 3.65.8 3c0-.65.55-1.2 1.2-1.2.65 0 1.2.55 1.2 1.2 0 .65-.55 1.2-1.2 1.2z"/></svg>
      <span itemprop="name">Pull requests</span>
      <span class="Counter">98</span>
      <meta itemprop="position" content="3">
</a>  </span>


    <a data-hotkey="g b" class="js-selected-navigation-item reponav-item" data-selected-links="repo_projects new_repo_project repo_project /kaltura/server/projects" href="/kaltura/server/projects">
      <svg class="octicon octicon-project" viewBox="0 0 15 16" version="1.1" width="15" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M10 12h3V2h-3v10zm-4-2h3V2H6v8zm-4 4h3V2H2v12zm-1 1h13V1H1v14zM14 0H1a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h13a1 1 0 0 0 1-1V1a1 1 0 0 0-1-1z"/></svg>
      Projects
      <span class="Counter" >0</span>
</a>


    <a class="js-selected-navigation-item reponav-item" data-selected-links="repo_graphs repo_contributors dependency_graph pulse alerts security /kaltura/server/pulse" href="/kaltura/server/pulse">
      <svg class="octicon octicon-graph" viewBox="0 0 16 16" version="1.1" width="16" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M16 14v1H0V0h1v14h15zM5 13H3V8h2v5zm4 0H7V3h2v10zm4 0h-2V6h2v7z"/></svg>
      Insights
</a>

</nav>


  </div>

<div class="container new-discussion-timeline experiment-repo-nav  ">
  <div class="repository-content ">

    
    

  
    <a class="d-none js-permalink-shortcut" data-hotkey="y" href="/kaltura/server/blob/2f7ae81730a9c1aaddee77fe27cab4dbeed621d0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh">Permalink</a>

    <!-- blob contrib key: blob_contributors:v21:ee91a445470550f26c4e085c813b1ea3 -->

        <div class="signup-prompt-bg rounded-1">
      <div class="signup-prompt p-4 text-center mb-4 rounded-1">
        <div class="position-relative">
          <!-- '"` --><!-- </textarea></xmp> --></option></form><form action="/site/dismiss_signup_prompt" accept-charset="UTF-8" method="post"><input name="utf8" type="hidden" value="&#x2713;" /><input type="hidden" name="authenticity_token" value="61xfrv3g5RU02RfQ0rPgPjI1TfymBNxWBeF0vI8lB2V5gRzroKhX/prsAods1IwvFd/cl1Bch0kp9ei5dN/NPA==" />
            <button type="submit" class="position-absolute top-0 right-0 btn-link link-gray" data-ga-click="(Logged out) Sign up prompt, clicked Dismiss, text:dismiss">
              Dismiss
            </button>
</form>          <h3 class="pt-2">Join GitHub today</h3>
          <p class="col-6 mx-auto">GitHub is home to over 28 million developers working together to host and review code, manage projects, and build software together.</p>
          <a class="btn btn-primary" href="/join?source=prompt-blob-show" data-ga-click="(Logged out) Sign up prompt, clicked Sign up, text:sign-up">Sign up</a>
        </div>
      </div>
    </div>


    <div class="file-navigation">
      
<div class="select-menu branch-select-menu js-menu-container js-select-menu float-left">
  <button class=" btn btn-sm select-menu-button js-menu-target css-truncate" data-hotkey="w"
    
    type="button" aria-label="Switch branches or tags" aria-expanded="false" aria-haspopup="true">
      <i>Branch:</i>
      <span class="js-select-button css-truncate-target">Naos-14.10.0</span>
  </button>

  <div class="select-menu-modal-holder js-menu-content js-navigation-container" data-pjax>

    <div class="select-menu-modal">
      <div class="select-menu-header">
        <svg class="octicon octicon-x js-menu-close" role="img" aria-label="Close" viewBox="0 0 12 16" version="1.1" width="12" height="16"><path fill-rule="evenodd" d="M7.48 8l3.75 3.75-1.48 1.48L6 9.48l-3.75 3.75-1.48-1.48L4.52 8 .77 4.25l1.48-1.48L6 6.52l3.75-3.75 1.48 1.48L7.48 8z"/></svg>
        <span class="select-menu-title">Switch branches/tags</span>
      </div>

      <tab-container>
      <div class="select-menu-filters">
        <div class="select-menu-text-filter">
          <input type="text" aria-label="Filter branches/tags" id="context-commitish-filter-field" class="form-control js-filterable-field js-navigation-enable" placeholder="Filter branches/tags">
        </div>
        <div class="select-menu-tabs" role="tablist">
          <ul>
            <li class="select-menu-tab">
              <button type="button" class="select-menu-tab-nav" data-filter-placeholder="Filter branches/tags" role="tab" aria-selected="true">Branches</button>
            </li>
            <li class="select-menu-tab">
              <button type="button" class="select-menu-tab-nav" data-filter-placeholder="Find a tag…" role="tab">Tags</button>
            </li>
          </ul>
        </div>
      </div>

      <div class="select-menu-list" role="tabpanel">
        <div data-filterable-for="context-commitish-filter-field" data-filterable-type="substring">


            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/11.2/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="11.2"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                11.2
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/11.2.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="11.2.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                11.2.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/13.3/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="13.3"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                13.3
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/13.18.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="13.18.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                13.18.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/14.8.9/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="14.8.9"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                14.8.9
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/1132/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="1132"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                1132
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/CR-179513/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="CR-179513"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                CR-179513
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/FEC-8276/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="FEC-8276"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                FEC-8276
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/FEC-8569/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="FEC-8569"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                FEC-8569
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/HuluDistributionProfile.php/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="HuluDistributionProfile.php"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                HuluDistributionProfile.php
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.1.0-transcoding-usage/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.1.0-transcoding-usage"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.1.0-transcoding-usage
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.1.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.1.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.1.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.2.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.2.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.2.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.2.1/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.2.1"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.2.1
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.3.0-SUP-987/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.3.0-SUP-987"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.3.0-SUP-987
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.3.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.3.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.3.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.3.1/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.3.1"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.3.1
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.4.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.4.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.4.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.5.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.5.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.5.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.6.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.6.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.6.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.7.0-playready-and-ism/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.7.0-playready-and-ism"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.7.0-playready-and-ism
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.7.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.7.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.7.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.8.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.8.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.8.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.9.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.9.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.9.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.10.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.10.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.10.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.11.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.11.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.11.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.11.1/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.11.1"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.11.1
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.12.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.12.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.12.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.13.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.13.0-rel"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.13.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.13.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.13.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.13.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.14.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.14.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.14.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.14.1/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.14.1"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.14.1
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.14.09/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.14.09"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.14.09
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.15.0-MyBranchForInstall/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.15.0-MyBranchForInstall"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.15.0-MyBranchForInstall
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.15.0-PLAT-524/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.15.0-PLAT-524"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.15.0-PLAT-524
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.15.0-PLAT-1083/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.15.0-PLAT-1083"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.15.0-PLAT-1083
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.15.0-PLAT-1122/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.15.0-PLAT-1122"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.15.0-PLAT-1122
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.15.0-PLAT-1252/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.15.0-PLAT-1252"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.15.0-PLAT-1252
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.15.0-SUP-934/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.15.0-SUP-934"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.15.0-SUP-934
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.15.0-playready-analytics-log/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.15.0-playready-analytics-log"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.15.0-playready-analytics-log
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.15.0-url-managers/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.15.0-url-managers"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.15.0-url-managers
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.15.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.15.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.15.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.16.0-Lecture-Capture/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.16.0-Lecture-Capture"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.16.0-Lecture-Capture
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.16.0-PLAT-1288/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.16.0-PLAT-1288"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.16.0-PLAT-1288
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.16.0-Sphinx/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.16.0-Sphinx"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.16.0-Sphinx
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.16.0-wsdl/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.16.0-wsdl"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.16.0-wsdl
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.16.0_SUP-2150/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.16.0_SUP-2150"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.16.0_SUP-2150
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.16.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.16.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.16.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.17.0-url-managers-fix/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.17.0-url-managers-fix"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.17.0-url-managers-fix
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.17.1/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.17.1"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.17.1
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.18.0-delete-ism-temp/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.18.0-delete-ism-temp"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.18.0-delete-ism-temp
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.18.0-rtmfp/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.18.0-rtmfp"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.18.0-rtmfp
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.18.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.18.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.18.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.19.0-blockMediaServerIndex/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.19.0-blockMediaServerIndex"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.19.0-blockMediaServerIndex
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.19.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.19.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.19.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.19.1-SUP-2513/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.19.1-SUP-2513"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.19.1-SUP-2513
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.19.1-fixSecretBug/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.19.1-fixSecretBug"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.19.1-fixSecretBug
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.19.1_CR-176401/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.19.1_CR-176401"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.19.1_CR-176401
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.19.1/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.19.1"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.19.1
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.19.2-LCPoc/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.19.2-LCPoc"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.19.2-LCPoc
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.19.2-generator-win-fix/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.19.2-generator-win-fix"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.19.2-generator-win-fix
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.19.2/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.19.2"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.19.2
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.19.3/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.19.3"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.19.3
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.19.4-PLAT-1946/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.19.4-PLAT-1946"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.19.4-PLAT-1946
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.19.4/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.19.4"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.19.4
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.19.5-DOCS/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.19.5-DOCS"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.19.5-DOCS
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.19.5-PLAT-1749/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.19.5-PLAT-1749"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.19.5-PLAT-1749
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.19.5/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.19.5"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.19.5
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.19.6-PLAT-1998/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.19.6-PLAT-1998"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.19.6-PLAT-1998
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.19.6/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.19.6"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.19.6
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.19.7-WEBC/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.19.7-WEBC"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.19.7-WEBC
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.19.7/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.19.7"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.19.7
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.19.8-PLAT-1987New/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.19.8-PLAT-1987New"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.19.8-PLAT-1987New
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.19.8/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.19.8"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.19.8
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.20.0-delivery-profiles/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.20.0-delivery-profiles"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.20.0-delivery-profiles
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.20.0-live-analytics/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.20.0-live-analytics"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.20.0-live-analytics
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.20.0_CR-176401/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.20.0_CR-176401"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.20.0_CR-176401
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX-9.20.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX-9.20.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX-9.20.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/IX%3D9.18.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="IX=9.18.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                IX=9.18.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.0.0-redundantCodeRemoval/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.0.0-redundantCodeRemoval"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.0.0-redundantCodeRemoval
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.0.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.0.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.0.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.1.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.1.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.1.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.2.0-PLAT-2289-AND-PLAT-2363-HOTFIX/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.2.0-PLAT-2289-AND-PLAT-2363-HOTFIX"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.2.0-PLAT-2289-AND-PLAT-2363-HOTFIX
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.2.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.2.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.2.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.3.0-KalturaDispatcherPatch/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.3.0-KalturaDispatcherPatch"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.3.0-KalturaDispatcherPatch
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.3.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.3.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.3.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.4.0-minusPlus/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.4.0-minusPlus"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.4.0-minusPlus
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.4.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.4.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.4.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.5.0-EM-877-PLAT-2399-IMX-and-SD/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.5.0-EM-877-PLAT-2399-IMX-and-SD"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.5.0-EM-877-PLAT-2399-IMX-and-SD
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.5.0-SUP-3864-improved/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.5.0-SUP-3864-improved"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.5.0-SUP-3864-improved
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.5.0-SUP-3894/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.5.0-SUP-3894"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.5.0-SUP-3894
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.5.0-fixTypoKontiki/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.5.0-fixTypoKontiki"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.5.0-fixTypoKontiki
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.5.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.5.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.5.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.6.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.6.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.6.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.7.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.7.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.7.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.8.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.8.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.8.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.9.0-inVideoQuiz/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.9.0-inVideoQuiz"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.9.0-inVideoQuiz
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.9.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.9.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.9.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.10.0-PLAT-2829-Optimization/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.10.0-PLAT-2829-Optimization"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.10.0-PLAT-2829-Optimization
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.10.0-PLAT-2883/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.10.0-PLAT-2883"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.10.0-PLAT-2883
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.10.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.10.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.10.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.12.0-PLAT-3013/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.12.0-PLAT-3013"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.12.0-PLAT-3013
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.12.0-tokenizer-time-from-header/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.12.0-tokenizer-time-from-header"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.12.0-tokenizer-time-from-header
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.12.0-update-release-notes-SUP-4739/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.12.0-update-release-notes-SUP-4739"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.12.0-update-release-notes-SUP-4739
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.12.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.12.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.12.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.13.0-PLAT-2042/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.13.0-PLAT-2042"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.13.0-PLAT-2042
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.13.0-PLAT-3008-tmp/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.13.0-PLAT-3008-tmp"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.13.0-PLAT-3008-tmp
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.13.0-PLAT-3074/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.13.0-PLAT-3074"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.13.0-PLAT-3074
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.13.0-PLAT3050/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.13.0-PLAT3050"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.13.0-PLAT3050
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.13.0-usefullScripts/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.13.0-usefullScripts"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.13.0-usefullScripts
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.13.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.13.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.13.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.14.0-PLAT-3059/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.14.0-PLAT-3059"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.14.0-PLAT-3059
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.14.0-PLAT-3088-1/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.14.0-PLAT-3088-1"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.14.0-PLAT-3088-1
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.14.0-PLAT-3137_1/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.14.0-PLAT-3137_1"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.14.0-PLAT-3137_1
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.14.0-PS-2268/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.14.0-PS-2268"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.14.0-PS-2268
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.14.0-SUP-4339/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.14.0-SUP-4339"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.14.0-SUP-4339
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.14.0-limitDPAction-array/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.14.0-limitDPAction-array"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.14.0-limitDPAction-array
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.14.0-update-version/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.14.0-update-version"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.14.0-update-version
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.14.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.14.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.14.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.15.0-PLAT-3348/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.15.0-PLAT-3348"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.15.0-PLAT-3348
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.15.0-SearchByUicoonf/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.15.0-SearchByUicoonf"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.15.0-SearchByUicoonf
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.15.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.15.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.15.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.16.0-non-ce/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.16.0-non-ce"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.16.0-non-ce
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.16.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.16.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.16.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.17.0-IVQ-cuepoint-add/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.17.0-IVQ-cuepoint-add"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.17.0-IVQ-cuepoint-add
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.17.0-PLAT-3025/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.17.0-PLAT-3025"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.17.0-PLAT-3025
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.17.0-PLAT-3477_1/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.17.0-PLAT-3477_1"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.17.0-PLAT-3477_1
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.17.0-PLAT-3485/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.17.0-PLAT-3485"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.17.0-PLAT-3485
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.17.0-PLAT-3575/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.17.0-PLAT-3575"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.17.0-PLAT-3575
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.17.0-PLAT-3595/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.17.0-PLAT-3595"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.17.0-PLAT-3595
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.17.0-PLAT-3619/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.17.0-PLAT-3619"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.17.0-PLAT-3619
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.17.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.17.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.17.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.18.0-IVQ-cuepoint-add_2/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.18.0-IVQ-cuepoint-add_2"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.18.0-IVQ-cuepoint-add_2
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.18.0-PLAT-3522/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.18.0-PLAT-3522"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.18.0-PLAT-3522
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.18.0-PLAT-3644/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.18.0-PLAT-3644"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.18.0-PLAT-3644
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.18.0-PLAT-3652-rn/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.18.0-PLAT-3652-rn"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.18.0-PLAT-3652-rn
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.18.0-PLAT-3652/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.18.0-PLAT-3652"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.18.0-PLAT-3652
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.18.0-PLAT-3702/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.18.0-PLAT-3702"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.18.0-PLAT-3702
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.18.0-SUP2038/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.18.0-SUP2038"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.18.0-SUP2038
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.18.0-SUP5673/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.18.0-SUP5673"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.18.0-SUP5673
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.18.0-ad/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.18.0-ad"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.18.0-ad
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.18.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.18.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.18.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.19.0-PLAT-3734-enforce-asset_entry-access/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.19.0-PLAT-3734-enforce-asset_entry-access"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.19.0-PLAT-3734-enforce-asset_entry-access
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.19.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.19.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.19.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.20.0-MediaPrep-Widevine/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.20.0-MediaPrep-Widevine"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.20.0-MediaPrep-Widevine
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.20.0-PLAT-3732/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.20.0-PLAT-3732"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.20.0-PLAT-3732
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.20.0-PLAT-3749/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.20.0-PLAT-3749"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.20.0-PLAT-3749
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.20.0-PLAT-3754/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.20.0-PLAT-3754"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.20.0-PLAT-3754
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.20.0-PLAT-3755/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.20.0-PLAT-3755"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.20.0-PLAT-3755
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.20.0-PLAT-3763/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.20.0-PLAT-3763"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.20.0-PLAT-3763
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.20.0-PLAT-3770/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.20.0-PLAT-3770"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.20.0-PLAT-3770
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.20.0-PLAT-3828/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.20.0-PLAT-3828"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.20.0-PLAT-3828
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.20.0-PLAT-3838/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.20.0-PLAT-3838"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.20.0-PLAT-3838
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.20.0-PLAT-3846/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.20.0-PLAT-3846"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.20.0-PLAT-3846
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.20.0-better-IVQ/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.20.0-better-IVQ"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.20.0-better-IVQ
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.20.0-fix-isAnonymous-cache/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.20.0-fix-isAnonymous-cache"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.20.0-fix-isAnonymous-cache
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.20.0-proxy-iframeembed/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.20.0-proxy-iframeembed"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.20.0-proxy-iframeembed
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.20.0-revert-MediaPrep-WideVine/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.20.0-revert-MediaPrep-WideVine"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.20.0-revert-MediaPrep-WideVine
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.20.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.20.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.20.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.21.0-SUP6187/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.21.0-SUP6187"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.21.0-SUP6187
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.21.0-SUP6258/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.21.0-SUP6258"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.21.0-SUP6258
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Jupiter-10.21.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Jupiter-10.21.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Jupiter-10.21.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Juptier-10.14.0-Fix-for-DB/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Juptier-10.14.0-Fix-for-DB"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Juptier-10.14.0-Fix-for-DB
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/KMS-8869-Refix-WM-rotation/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="KMS-8869-Refix-WM-rotation"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                KMS-8869-Refix-WM-rotation
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/KMS-10491-fix-getServeUrl/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="KMS-10491-fix-getServeUrl"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                KMS-10491-fix-getServeUrl
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.0.0-KMS-8680/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.0.0-KMS-8680"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.0.0-KMS-8680
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.0.0-PLAT-3915/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.0.0-PLAT-3915"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.0.0-PLAT-3915
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.0.0-PLAT-3920-noam/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.0.0-PLAT-3920-noam"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.0.0-PLAT-3920-noam
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.0.0-PLAT-3975/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.0.0-PLAT-3975"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.0.0-PLAT-3975
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.0.0-SUP6258/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.0.0-SUP6258"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.0.0-SUP6258
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.0.0-per-partner-caching-headers/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.0.0-per-partner-caching-headers"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.0.0-per-partner-caching-headers
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.0.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.0.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.0.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.1.0-PLAT-3957/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.1.0-PLAT-3957"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.1.0-PLAT-3957
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.1.0-PLAT-3975/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.1.0-PLAT-3975"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.1.0-PLAT-3975
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.1.0-PLAT-4017/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.1.0-PLAT-4017"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.1.0-PLAT-4017
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.1.0-PLAT-4070/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.1.0-PLAT-4070"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.1.0-PLAT-4070
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.1.0-addServerNodeTypeToQuery/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.1.0-addServerNodeTypeToQuery"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.1.0-addServerNodeTypeToQuery
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.1.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.1.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.1.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.2.0-DropFolder-sync-optimize-sleep/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.2.0-DropFolder-sync-optimize-sleep"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.2.0-DropFolder-sync-optimize-sleep
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.2.0-PLAT-3896/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.2.0-PLAT-3896"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.2.0-PLAT-3896
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.2.0-PLAT-3961/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.2.0-PLAT-3961"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.2.0-PLAT-3961
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.2.0-SUP-6218/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.2.0-SUP-6218"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.2.0-SUP-6218
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.2.0-SUP-6269/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.2.0-SUP-6269"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.2.0-SUP-6269
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.2.0-TM/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.2.0-TM"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.2.0-TM
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.2.0-revert-for-butrus/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.2.0-revert-for-butrus"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.2.0-revert-for-butrus
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.2.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.2.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.2.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.3.0-PLAT-4632/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.3.0-PLAT-4632"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.3.0-PLAT-4632
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.3.0-SUP-4598/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.3.0-SUP-4598"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.3.0-SUP-4598
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.3.0-SUP-5613/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.3.0-SUP-5613"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.3.0-SUP-5613
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.3.0-TR-948/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.3.0-TR-948"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.3.0-TR-948
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.3.0-limit-custom_data-size/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.3.0-limit-custom_data-size"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.3.0-limit-custom_data-size
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.3.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.3.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.3.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.4.0-filterOnlyVodDP/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.4.0-filterOnlyVodDP"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.4.0-filterOnlyVodDP
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.4.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.4.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.4.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.5.0-BullsEye/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.5.0-BullsEye"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.5.0-BullsEye
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.5.0-SUP-6620/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.5.0-SUP-6620"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.5.0-SUP-6620
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.5.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.5.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.5.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.6.0-PLAT-4002/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.6.0-PLAT-4002"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.6.0-PLAT-4002
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.6.0-PLAT-4842/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.6.0-PLAT-4842"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.6.0-PLAT-4842
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.6.0-PLAT-4934/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.6.0-PLAT-4934"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.6.0-PLAT-4934
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.6.0-SUP4535/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.6.0-SUP4535"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.6.0-SUP4535
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.6.0-TM-RC-01/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.6.0-TM-RC-01"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.6.0-TM-RC-01
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.6.0-cache-patch/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.6.0-cache-patch"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.6.0-cache-patch
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.6.0-fixDumpRequest/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.6.0-fixDumpRequest"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.6.0-fixDumpRequest
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.6.0-flavorAssetFix/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.6.0-flavorAssetFix"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.6.0-flavorAssetFix
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.6.0-truncate-selected-time-params/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.6.0-truncate-selected-time-params"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.6.0-truncate-selected-time-params
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.6.0-update_php_type_fix/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.6.0-update_php_type_fix"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.6.0-update_php_type_fix
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.6.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.6.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.6.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.7.0-nginx-live/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.7.0-nginx-live"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.7.0-nginx-live
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.7.0-parallel-chunks-uploads/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.7.0-parallel-chunks-uploads"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.7.0-parallel-chunks-uploads
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.7.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.7.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.7.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.8.0-PLAT-4020/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.8.0-PLAT-4020"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.8.0-PLAT-4020
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.8.0-ad-stitching-poc/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.8.0-ad-stitching-poc"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.8.0-ad-stitching-poc
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.8.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.8.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.8.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.9.0-PLAT-5126/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.9.0-PLAT-5126"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.9.0-PLAT-5126
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.9.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.9.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.9.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.10.0-PLAT-4991/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.10.0-PLAT-4991"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.10.0-PLAT-4991
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.10.0-PLAT-5207-2/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.10.0-PLAT-5207-2"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.10.0-PLAT-5207-2
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.10.0-PLAT-5207/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.10.0-PLAT-5207"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.10.0-PLAT-5207
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.10.0-SUP-7707/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.10.0-SUP-7707"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.10.0-SUP-7707
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.10.0-enforce-flvclipper-access-control/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.10.0-enforce-flvclipper-access-control"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.10.0-enforce-flvclipper-access-control
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.10.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.10.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.10.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.11.0-PLAT-5207/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.11.0-PLAT-5207"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.11.0-PLAT-5207
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.11.0-SUP-5851/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.11.0-SUP-5851"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.11.0-SUP-5851
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.11.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.11.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.11.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.12.0-BigRedButtonPOC/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.12.0-BigRedButtonPOC"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.12.0-BigRedButtonPOC
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.12.0-PLAT-4644-admin-ui-for-xslt/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.12.0-PLAT-4644-admin-ui-for-xslt"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.12.0-PLAT-4644-admin-ui-for-xslt
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.12.0-PLAT-5343/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.12.0-PLAT-5343"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.12.0-PLAT-5343
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.12.0-PLAT-5346/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.12.0-PLAT-5346"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.12.0-PLAT-5346
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.12.0-SUP7619/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.12.0-SUP7619"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.12.0-SUP7619
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.12.0-node-client-modifications/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.12.0-node-client-modifications"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.12.0-node-client-modifications
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.13.0-KMS-11392/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.13.0-KMS-11392"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.13.0-KMS-11392
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.13.0-PLAT-5354/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.13.0-PLAT-5354"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.13.0-PLAT-5354
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.13.0-PLAT-5534/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.13.0-PLAT-5534"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.13.0-PLAT-5534
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.13.0-harmonic/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.13.0-harmonic"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.13.0-harmonic
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.13.0-no-capture-space-in-clients/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.13.0-no-capture-space-in-clients"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.13.0-no-capture-space-in-clients
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.13.0-node-client-modifications/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.13.0-node-client-modifications"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.13.0-node-client-modifications
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.13.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.13.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.13.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.14.0-KMS-11327-PLAT-5350/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.14.0-KMS-11327-PLAT-5350"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.14.0-KMS-11327-PLAT-5350
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.14.0-PLAT-3025/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.14.0-PLAT-3025"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.14.0-PLAT-3025
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.14.0-SUP7459/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.14.0-SUP7459"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.14.0-SUP7459
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.14.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.14.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.14.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.15.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.15.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.15.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.16.0-PLAT-5496/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.16.0-PLAT-5496"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.16.0-PLAT-5496
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.16.0-PLAT-5543A/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.16.0-PLAT-5543A"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.16.0-PLAT-5543A
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.16.0-kVoicebaseFlowManager-line-126-fatal-error/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.16.0-kVoicebaseFlowManager-line-126-fatal-error"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.16.0-kVoicebaseFlowManager-line-126-fatal-error
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.16.0-limit-caption-size-for-index/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.16.0-limit-caption-size-for-index"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.16.0-limit-caption-size-for-index
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.16.0-mediaprep-aspera/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.16.0-mediaprep-aspera"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.16.0-mediaprep-aspera
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.16.0-remove-testme-tesmedoc-from-repo/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.16.0-remove-testme-tesmedoc-from-repo"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.16.0-remove-testme-tesmedoc-from-repo
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.16.0-thumb-remove-dest-file/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.16.0-thumb-remove-dest-file"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.16.0-thumb-remove-dest-file
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.16.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.16.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.16.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.17.0-FEC-5620-try2/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.17.0-FEC-5620-try2"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.17.0-FEC-5620-try2
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.17.0-PLAT-5640-ContentAwareEncoding/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.17.0-PLAT-5640-ContentAwareEncoding"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.17.0-PLAT-5640-ContentAwareEncoding
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.17.0-generator/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.17.0-generator"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.17.0-generator
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.17.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.17.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.17.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.18.0-moveLiveEntryServerNodeCacheLogic/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.18.0-moveLiveEntryServerNodeCacheLogic"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.18.0-moveLiveEntryServerNodeCacheLogic
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.18.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.18.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.18.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.19.0-PLAT-4439-MultiStream/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.19.0-PLAT-4439-MultiStream"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.19.0-PLAT-4439-MultiStream
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.19.0-PLAT-5414-versionFix/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.19.0-PLAT-5414-versionFix"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.19.0-PLAT-5414-versionFix
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.19.0-PLAT-5725/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.19.0-PLAT-5725"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.19.0-PLAT-5725
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.19.0-PLAT-5747/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.19.0-PLAT-5747"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.19.0-PLAT-5747
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.19.0-Support-S3-Compatible-Storage/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.19.0-Support-S3-Compatible-Storage"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.19.0-Support-S3-Compatible-Storage
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.19.0-addLiveEntryServerNodeDcColmun/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.19.0-addLiveEntryServerNodeDcColmun"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.19.0-addLiveEntryServerNodeDcColmun
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.19.0-apimon-save-file/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.19.0-apimon-save-file"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.19.0-apimon-save-file
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.19.0-atar/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.19.0-atar"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.19.0-atar
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.19.0-export-to-S3-compatible-fix-typo/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.19.0-export-to-S3-compatible-fix-typo"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.19.0-export-to-S3-compatible-fix-typo
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.19.0-external-thumb-capture/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.19.0-external-thumb-capture"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.19.0-external-thumb-capture
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.19.0-fix-admin-console-S3-Siganture-type/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.19.0-fix-admin-console-S3-Siganture-type"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.19.0-fix-admin-console-S3-Siganture-type
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.19.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.19.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.19.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.20.0-external-thumb-capture-tokenization/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.20.0-external-thumb-capture-tokenization"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.20.0-external-thumb-capture-tokenization
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.20.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.20.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.20.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.21.0-CloudFront-IP-Token/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.21.0-CloudFront-IP-Token"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.21.0-CloudFront-IP-Token
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.21.0-FixFlavorAssetServiceTypo/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.21.0-FixFlavorAssetServiceTypo"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.21.0-FixFlavorAssetServiceTypo
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.21.0-PLAT-5742-fix/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.21.0-PLAT-5742-fix"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.21.0-PLAT-5742-fix
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.21.0-Webcasting-upgrade-service/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.21.0-Webcasting-upgrade-service"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.21.0-Webcasting-upgrade-service
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.21.0-cache-entitlement-check/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.21.0-cache-entitlement-check"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.21.0-cache-entitlement-check
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kajam-11.21.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kajam-11.21.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kajam-11.21.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Kaljam-11.6.0-populate-init-update/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Kaljam-11.6.0-populate-init-update"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Kaljam-11.6.0-populate-init-update
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.0.0-PLAT-3853/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.0.0-PLAT-3853"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.0.0-PLAT-3853
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.0.0-PlayServerGroupCuepointPermissions/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.0.0-PlayServerGroupCuepointPermissions"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.0.0-PlayServerGroupCuepointPermissions
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.0.0-avoidCallingKuserPeerForEachCuePoint/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.0.0-avoidCallingKuserPeerForEachCuePoint"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.0.0-avoidCallingKuserPeerForEachCuePoint
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.0.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.0.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.0.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.1.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.1.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.1.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.2.0-fixKjobsSuspender/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.2.0-fixKjobsSuspender"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.2.0-fixKjobsSuspender
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.2.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.2.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.2.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.3.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.3.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.3.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.4.0-fixUploadTokenWithNoExt/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.4.0-fixUploadTokenWithNoExt"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.4.0-fixUploadTokenWithNoExt
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.4.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.4.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.4.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.5.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.5.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.5.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.6.0-PLAT-6257/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.6.0-PLAT-6257"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.6.0-PLAT-6257
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.6.0-PLAT-6496/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.6.0-PLAT-6496"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.6.0-PLAT-6496
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.6.0-batchjob-history-reduce-limit/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.6.0-batchjob-history-reduce-limit"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.6.0-batchjob-history-reduce-limit
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.6.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.6.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.6.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.7.0-PLAT-6283/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.7.0-PLAT-6283"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.7.0-PLAT-6283
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.7.0-PLAT-6557/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.7.0-PLAT-6557"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.7.0-PLAT-6557
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.7.0-PLAT-6611/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.7.0-PLAT-6611"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.7.0-PLAT-6611
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.7.0-TR-1607-addition/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.7.0-TR-1607-addition"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.7.0-TR-1607-addition
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.7.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.7.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.7.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.8.0-PLAT-6557/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.8.0-PLAT-6557"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.8.0-PLAT-6557
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.8.0-PLAT-6647/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.8.0-PLAT-6647"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.8.0-PLAT-6647
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.8.0-PLAT-6681/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.8.0-PLAT-6681"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.8.0-PLAT-6681
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.8.0-PLAT-7504/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.8.0-PLAT-7504"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.8.0-PLAT-7504
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.8.0-hackathonAdStichOverlay/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.8.0-hackathonAdStichOverlay"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.8.0-hackathonAdStichOverlay
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.8.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.8.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.8.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.9.0-PLAT-6648/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.9.0-PLAT-6648"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.9.0-PLAT-6648
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.9.0-PLAT-6762/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.9.0-PLAT-6762"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.9.0-PLAT-6762
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.9.0-PLAT-6786-Conditional-ConvProf/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.9.0-PLAT-6786-Conditional-ConvProf"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.9.0-PLAT-6786-Conditional-ConvProf
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.9.0-PLAT-6831/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.9.0-PLAT-6831"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.9.0-PLAT-6831
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.9.0-PLAT-PLAT-6855/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.9.0-PLAT-PLAT-6855"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.9.0-PLAT-PLAT-6855
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.9.0-PS-3118-Relative-WM-Position/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.9.0-PS-3118-Relative-WM-Position"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.9.0-PS-3118-Relative-WM-Position
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.9.0-TR-1205/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.9.0-TR-1205"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.9.0-TR-1205
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.9.0-TR-1761-revert/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.9.0-TR-1761-revert"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.9.0-TR-1761-revert
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.9.0-TR-1770/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.9.0-TR-1770"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.9.0-TR-1770
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.9.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.9.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.9.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.10.0-PLAT-6855/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.10.0-PLAT-6855"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.10.0-PLAT-6855
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.10.0-add-singleProcessExecutorScript/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.10.0-add-singleProcessExecutorScript"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.10.0-add-singleProcessExecutorScript
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.11.0-Elastic/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.11.0-Elastic"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.11.0-Elastic
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.11.0-PLAT-6786-Conditional-ConvProf/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.11.0-PLAT-6786-Conditional-ConvProf"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.11.0-PLAT-6786-Conditional-ConvProf
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.11.0-PLAT-7006/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.11.0-PLAT-7006"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.11.0-PLAT-7006
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.11.0-refactorPrepareDistributionJob/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.11.0-refactorPrepareDistributionJob"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.11.0-refactorPrepareDistributionJob
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.13.0-PLAT-7263/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.13.0-PLAT-7263"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.13.0-PLAT-7263
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.13.0-ViewHistory/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.13.0-ViewHistory"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.13.0-ViewHistory
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.13.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.13.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.13.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.14.0-PLAT-7022/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.14.0-PLAT-7022"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.14.0-PLAT-7022
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.14.0-PLAT-7178-EAC3/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.14.0-PLAT-7178-EAC3"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.14.0-PLAT-7178-EAC3
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.14.0-PLAT-7235/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.14.0-PLAT-7235"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.14.0-PLAT-7235
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.14.0-PLAT-7273/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.14.0-PLAT-7273"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.14.0-PLAT-7273
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.14.0-PLAT-7293/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.14.0-PLAT-7293"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.14.0-PLAT-7293
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.14.0-PS-2941-A/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.14.0-PS-2941-A"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.14.0-PS-2941-A
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.14.0-PS-2983/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.14.0-PS-2983"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.14.0-PS-2983
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.14.0-ViewHistoryRevert/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.14.0-ViewHistoryRevert"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.14.0-ViewHistoryRevert
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.14.0-add-cb-queries-logs/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.14.0-add-cb-queries-logs"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.14.0-add-cb-queries-logs
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.14.0-add-couchbase-log/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.14.0-add-couchbase-log"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.14.0-add-couchbase-log
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.14.0-disable-IRelatedObject-for-dropfolders/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.14.0-disable-IRelatedObject-for-dropfolders"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.14.0-disable-IRelatedObject-for-dropfolders
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.14.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.14.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.14.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.15.0-PS-2993/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.15.0-PS-2993"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.15.0-PS-2993
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.15.0-anonymous-ip-acl/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.15.0-anonymous-ip-acl"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.15.0-anonymous-ip-acl
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.16.0_sqlinjection_reports/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.16.0_sqlinjection_reports"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.16.0_sqlinjection_reports
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.16.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.16.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.16.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.17.0-/PLAT-7460/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.17.0-/PLAT-7460"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.17.0-/PLAT-7460
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.17.0-PLAT-7082/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.17.0-PLAT-7082"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.17.0-PLAT-7082
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.17.0-PLAT-7343/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.17.0-PLAT-7343"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.17.0-PLAT-7343
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.17.0-PLAT-7460/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.17.0-PLAT-7460"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.17.0-PLAT-7460
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.17.0-PLAT-7466/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.17.0-PLAT-7466"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.17.0-PLAT-7466
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.17.0-PS-3067/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.17.0-PS-3067"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.17.0-PS-3067
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.17.0-PS-3068/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.17.0-PS-3068"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.17.0-PS-3068
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.17.0-PS-3070/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.17.0-PS-3070"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.17.0-PS-3070
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.17.0_userEntry_update_player_permission/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.17.0_userEntry_update_player_permission"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.17.0_userEntry_update_player_permission
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.17.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.17.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.17.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.18.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.18.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.18.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.19.0-PLAT-7675_1/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.19.0-PLAT-7675_1"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.19.0-PLAT-7675_1
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.19.0-PLAT-7741/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.19.0-PLAT-7741"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.19.0-PLAT-7741
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.19.0-elastic_stg/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.19.0-elastic_stg"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.19.0-elastic_stg
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.19.0-php7/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.19.0-php7"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.19.0-php7
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.19.0-revertDropFolderFix/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.19.0-revertDropFolderFix"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.19.0-revertDropFolderFix
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.19.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.19.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.19.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.20.0-DigitalElement-GeoCoder/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.20.0-DigitalElement-GeoCoder"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.20.0-DigitalElement-GeoCoder
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.20.0-PLAT-7708/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.20.0-PLAT-7708"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.20.0-PLAT-7708
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Lynx-12.20.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Lynx-12.20.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Lynx-12.20.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.0.0-PLAT-7780/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.0.0-PLAT-7780"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.0.0-PLAT-7780
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.0.0-PLAT-7801-Chunked-FFmpeg/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.0.0-PLAT-7801-Chunked-FFmpeg"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.0.0-PLAT-7801-Chunked-FFmpeg
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.0.0-PLAT-7810-A/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.0.0-PLAT-7810-A"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.0.0-PLAT-7810-A
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.0.0-SUPPS-1191-ServeFlavorDCRedirectFix/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.0.0-SUPPS-1191-ServeFlavorDCRedirectFix"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.0.0-SUPPS-1191-ServeFlavorDCRedirectFix
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.0.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.0.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.0.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.1.0-DPtidyCode/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.1.0-DPtidyCode"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.1.0-DPtidyCode
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.1.0-TR-1951/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.1.0-TR-1951"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.1.0-TR-1951
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.1.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.1.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.1.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.2.0-7969/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.2.0-7969"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.2.0-7969
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.2.0-PLAT-7481/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.2.0-PLAT-7481"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.2.0-PLAT-7481
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.2.0-PLAT-7854/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.2.0-PLAT-7854"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.2.0-PLAT-7854
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.2.0-PLAT-7943/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.2.0-PLAT-7943"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.2.0-PLAT-7943
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.2.0-PLAT-7969/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.2.0-PLAT-7969"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.2.0-PLAT-7969
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.2.0-audio-upload-fix/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.2.0-audio-upload-fix"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.2.0-audio-upload-fix
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.2.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.2.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.2.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.3.0-KMS-15029-1/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.3.0-KMS-15029-1"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.3.0-KMS-15029-1
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.3.0-KMS-15029/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.3.0-KMS-15029"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.3.0-KMS-15029
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.3.0-beacon_for_ecdn/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.3.0-beacon_for_ecdn"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.3.0-beacon_for_ecdn
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.3.0-disable-elastic-insertion/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.3.0-disable-elastic-insertion"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.3.0-disable-elastic-insertion
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.3.0-returnFalse/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.3.0-returnFalse"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.3.0-returnFalse
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.3.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.3.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.3.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.4.0-SUP-12069/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.4.0-SUP-12069"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.4.0-SUP-12069
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.4.0-plat-/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.4.0-plat-"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.4.0-plat-
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.4.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.4.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.4.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.5.0-AutoFinalizeToCustomData/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.5.0-AutoFinalizeToCustomData"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.5.0-AutoFinalizeToCustomData
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.5.0-PLAT-8134/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.5.0-PLAT-8134"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.5.0-PLAT-8134
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.5.0-addUiConfToBundle/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.5.0-addUiConfToBundle"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.5.0-addUiConfToBundle
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.5.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.5.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.5.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.6.0-KMS-15714/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.6.0-KMS-15714"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.6.0-KMS-15714
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.6.0-PLAT-8169/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.6.0-PLAT-8169"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.6.0-PLAT-8169
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.6.0-SUP-11979-Thumbs-deinterlace/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.6.0-SUP-11979-Thumbs-deinterlace"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.6.0-SUP-11979-Thumbs-deinterlace
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.6.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.6.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.6.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.7.0-KMS-15931/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.7.0-KMS-15931"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.7.0-KMS-15931
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.7.0-PLAT-8134/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.7.0-PLAT-8134"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.7.0-PLAT-8134
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.7.0-PLAT-8231/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.7.0-PLAT-8231"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.7.0-PLAT-8231
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.7.0-PLAT-8236/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.7.0-PLAT-8236"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.7.0-PLAT-8236
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.7.0-PLAT-8251-vendorProfile-adminConsole/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.7.0-PLAT-8251-vendorProfile-adminConsole"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.7.0-PLAT-8251-vendorProfile-adminConsole
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.7.0-PLAT-8251-yossi1/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.7.0-PLAT-8251-yossi1"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.7.0-PLAT-8251-yossi1
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.7.0-Plta-8231/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.7.0-Plta-8231"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.7.0-Plta-8231
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.7.0-move-convert-count-check/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.7.0-move-convert-count-check"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.7.0-move-convert-count-check
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.7.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.7.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.7.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.8.0-PLAT-8043/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.8.0-PLAT-8043"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.8.0-PLAT-8043
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.8.0-PLAT-8315/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.8.0-PLAT-8315"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.8.0-PLAT-8315
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.8.0-SUP-12623/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.8.0-SUP-12623"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.8.0-SUP-12623
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.8.0-SUP-12885/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.8.0-SUP-12885"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.8.0-SUP-12885
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.8.0-optimize_access_to_priorityGroup_table/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.8.0-optimize_access_to_priorityGroup_table"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.8.0-optimize_access_to_priorityGroup_table
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.8.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.8.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.8.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.9.0-PLAT-8233/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.9.0-PLAT-8233"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.9.0-PLAT-8233
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.9.0-PLAT-8312/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.9.0-PLAT-8312"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.9.0-PLAT-8312
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.9.0-PLAT-8366/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.9.0-PLAT-8366"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.9.0-PLAT-8366
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.9.0-PLAT-8379/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.9.0-PLAT-8379"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.9.0-PLAT-8379
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.9.0-SUP-11979-Thumbs-deinterlace/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.9.0-SUP-11979-Thumbs-deinterlace"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.9.0-SUP-11979-Thumbs-deinterlace
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.9.0-elastic-integration-PLAT-8312/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.9.0-elastic-integration-PLAT-8312"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.9.0-elastic-integration-PLAT-8312
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.9.0-elastic-integration-PLAT-8412/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.9.0-elastic-integration-PLAT-8412"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.9.0-elastic-integration-PLAT-8412
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.9.0-elastic-integration/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.9.0-elastic-integration"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.9.0-elastic-integration
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.9.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.9.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.9.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.10.0-Json-serializer-fix/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.10.0-Json-serializer-fix"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.10.0-Json-serializer-fix
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.10.0-PLAT-8336/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.10.0-PLAT-8336"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.10.0-PLAT-8336
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.10.0-PLAT-8410/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.10.0-PLAT-8410"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.10.0-PLAT-8410
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.10.0-elastic-integration-eSearchMetaDataItem/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.10.0-elastic-integration-eSearchMetaDataItem"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.10.0-elastic-integration-eSearchMetaDataItem
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.10.0-enable_kave_for_partner/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.10.0-enable_kave_for_partner"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.10.0-enable_kave_for_partner
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.10.0-hackathon3/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.10.0-hackathon3"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.10.0-hackathon3
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.10.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.10.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.10.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.11.0-Hackathon-2017-DataLayer/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.11.0-Hackathon-2017-DataLayer"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.11.0-Hackathon-2017-DataLayer
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.11.0-TR-2048/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.11.0-TR-2048"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.11.0-TR-2048
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.11.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.11.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.11.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.12.0-KMS-16573/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.12.0-KMS-16573"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.12.0-KMS-16573
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.12.0-KMS-16844/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.12.0-KMS-16844"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.12.0-KMS-16844
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.12.0-PLAT-8468/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.12.0-PLAT-8468"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.12.0-PLAT-8468
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.12.0-PLAT-8503/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.12.0-PLAT-8503"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.12.0-PLAT-8503
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.12.0-SUPPS-1422/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.12.0-SUPPS-1422"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.12.0-SUPPS-1422
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.12.0-TR-2095/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.12.0-TR-2095"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.12.0-TR-2095
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.12.0-rtc/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.12.0-rtc"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.12.0-rtc
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.12.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.12.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.12.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.13.0-PLAT-8547/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.13.0-PLAT-8547"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.13.0-PLAT-8547
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.13.0-PLAT-8548/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.13.0-PLAT-8548"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.13.0-PLAT-8548
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.13.0-PLAT-8550/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.13.0-PLAT-8550"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.13.0-PLAT-8550
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.13.0-TR-2059/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.13.0-TR-2059"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.13.0-TR-2059
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.13.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.13.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.13.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.14.0-PLAT-8590/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.14.0-PLAT-8590"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.14.0-PLAT-8590
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.14.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.14.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.14.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.15.0-PLAT-8695/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.15.0-PLAT-8695"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.15.0-PLAT-8695
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.15.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.15.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.15.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.16.0-LEC-809/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.16.0-LEC-809"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.16.0-LEC-809
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.16.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.16.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.16.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.17.0-PLAT-8553/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.17.0-PLAT-8553"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.17.0-PLAT-8553
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.17.0-PSVAMB-2692/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.17.0-PSVAMB-2692"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.17.0-PSVAMB-2692
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.17.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.17.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.17.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.18.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.18.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.18.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.19.0-PLAT-8844/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.19.0-PLAT-8844"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.19.0-PLAT-8844
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.19.0-Plat8885/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.19.0-Plat8885"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.19.0-Plat8885
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.19.0-allowPasssingPlaybackContext/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.19.0-allowPasssingPlaybackContext"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.19.0-allowPasssingPlaybackContext
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.19.0-sphonxSaveImprovements/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.19.0-sphonxSaveImprovements"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.19.0-sphonxSaveImprovements
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.19.0-sup-14047/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.19.0-sup-14047"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.19.0-sup-14047
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.19.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.19.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.19.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.20-Reach-creditSyncFix/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.20-Reach-creditSyncFix"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.20-Reach-creditSyncFix
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.20.0-PLAT-7447/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.20.0-PLAT-7447"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.20.0-PLAT-7447
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.20.0-PLAT-8583/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.20.0-PLAT-8583"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.20.0-PLAT-8583
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.20.0-PLAT-8880-entry-version-increment/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.20.0-PLAT-8880-entry-version-increment"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.20.0-PLAT-8880-entry-version-increment
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.20.0-PLAT-8880/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.20.0-PLAT-8880"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.20.0-PLAT-8880
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.20.0-PSVAMB-3216/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.20.0-PSVAMB-3216"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.20.0-PSVAMB-3216
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.20.0-PSVAMB-3226/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.20.0-PSVAMB-3226"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.20.0-PSVAMB-3226
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.20.0-SUP-14520-chunks_with_faststart/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.20.0-SUP-14520-chunks_with_faststart"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.20.0-SUP-14520-chunks_with_faststart
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.20.0-categoryKuserSphinxCache/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.20.0-categoryKuserSphinxCache"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.20.0-categoryKuserSphinxCache
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Mercury-13.20.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Mercury-13.20.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Mercury-13.20.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.0.0-PLAT-6872-RemoveExcludeSourceFromAdminConsole/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.0.0-PLAT-6872-RemoveExcludeSourceFromAdminConsole"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.0.0-PLAT-6872-RemoveExcludeSourceFromAdminConsole
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.0.0-PLAT-8583/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.0.0-PLAT-8583"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.0.0-PLAT-8583
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.0.0-PLAT-8917/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.0.0-PLAT-8917"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.0.0-PLAT-8917
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.0.0-PLAT-8973-AllowForceStreamType/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.0.0-PLAT-8973-AllowForceStreamType"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.0.0-PLAT-8973-AllowForceStreamType
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.0.0-PLAT-8975/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.0.0-PLAT-8975"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.0.0-PLAT-8975
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.0.0-PLAT-8976/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.0.0-PLAT-8976"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.0.0-PLAT-8976
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.0.0-PLAT-9005-CancelReplaceWhenTrimLive/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.0.0-PLAT-9005-CancelReplaceWhenTrimLive"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.0.0-PLAT-9005-CancelReplaceWhenTrimLive
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.0.0-SUP-14517-inaccurate-fps-with-CE/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.0.0-SUP-14517-inaccurate-fps-with-CE"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.0.0-SUP-14517-inaccurate-fps-with-CE
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.0.0-SUPPS-1476/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.0.0-SUPPS-1476"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.0.0-SUPPS-1476
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.0.0-WEBC-1195/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.0.0-WEBC-1195"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.0.0-WEBC-1195
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.0.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.0.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.0.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.0.1-PLAT-8940/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.0.1-PLAT-8940"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.0.1-PLAT-8940
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.1.0-HF-SUP-14444/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.1.0-HF-SUP-14444"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.1.0-HF-SUP-14444
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.1.0-KMS-17960-EntryStatusAndDuration/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.1.0-KMS-17960-EntryStatusAndDuration"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.1.0-KMS-17960-EntryStatusAndDuration
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.1.0-PLAT-8560/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.1.0-PLAT-8560"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.1.0-PLAT-8560
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.1.0-PLAT-8952-AddLastSlideCase/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.1.0-PLAT-8952-AddLastSlideCase"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.1.0-PLAT-8952-AddLastSlideCase
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.1.0-PLAT-9062/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.1.0-PLAT-9062"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.1.0-PLAT-9062
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.1.0-support-ipv6/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.1.0-support-ipv6"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.1.0-support-ipv6
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.1.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.1.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.1.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.2.0-MR_adminConsoleFix/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.2.0-MR_adminConsoleFix"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.2.0-MR_adminConsoleFix
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.2.0-PLAT-9019/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.2.0-PLAT-9019"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.2.0-PLAT-9019
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.2.0-PLAT-9064/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.2.0-PLAT-9064"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.2.0-PLAT-9064
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.2.0-PLAT-9603/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.2.0-PLAT-9603"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.2.0-PLAT-9603
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.2.0-PLAT9100-2/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.2.0-PLAT9100-2"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.2.0-PLAT9100-2
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.2.0-SUP-14444/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.2.0-SUP-14444"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.2.0-SUP-14444
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.2.0-SUP-14941/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.2.0-SUP-14941"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.2.0-SUP-14941
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.2.0-reachCreditDatesFix/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.2.0-reachCreditDatesFix"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.2.0-reachCreditDatesFix
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.2.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.2.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.2.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.3.0-BlockInternalUrls/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.3.0-BlockInternalUrls"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.3.0-BlockInternalUrls
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.3.0-PLAT-8904/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.3.0-PLAT-8904"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.3.0-PLAT-8904
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.3.0-PLAT-8908/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.3.0-PLAT-8908"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.3.0-PLAT-8908
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.3.0-PLAT-9096/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.3.0-PLAT-9096"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.3.0-PLAT-9096
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.3.0-PLAT-9100-ResponseProfile-Fix/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.3.0-PLAT-9100-ResponseProfile-Fix"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.3.0-PLAT-9100-ResponseProfile-Fix
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.3.0-PLAT-9101/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.3.0-PLAT-9101"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.3.0-PLAT-9101
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.3.0-PLAT-9106/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.3.0-PLAT-9106"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.3.0-PLAT-9106
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.3.0-SUP-14718/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.3.0-SUP-14718"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.3.0-SUP-14718
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.3.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.3.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.3.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.4.0-PLAT-9012-multiple-admin-secret/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.4.0-PLAT-9012-multiple-admin-secret"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.4.0-PLAT-9012-multiple-admin-secret
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.4.0-PLAT-9130-Limit-max-Dim-Thumb/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.4.0-PLAT-9130-Limit-max-Dim-Thumb"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.4.0-PLAT-9130-Limit-max-Dim-Thumb
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.4.0-cost-base-multi-cdn-support/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.4.0-cost-base-multi-cdn-support"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.4.0-cost-base-multi-cdn-support
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.4.0-entry_dist_sphinx_optimization/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.4.0-entry_dist_sphinx_optimization"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.4.0-entry_dist_sphinx_optimization
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.4.0-kmcng-1926/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.4.0-kmcng-1926"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.4.0-kmcng-1926
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.4.0-limit-bulk-list-query/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.4.0-limit-bulk-list-query"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.4.0-limit-bulk-list-query
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.4.0-safe_file_get_contents/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.4.0-safe_file_get_contents"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.4.0-safe_file_get_contents
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.4.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.4.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.4.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.5.0-PLAT-9163/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.5.0-PLAT-9163"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.5.0-PLAT-9163
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.5.0-PSVAMB-4099/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.5.0-PSVAMB-4099"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.5.0-PSVAMB-4099
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.5.0-SUP-15188/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.5.0-SUP-15188"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.5.0-SUP-15188
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.5.0-SUP-15564/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.5.0-SUP-15564"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.5.0-SUP-15564
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.5.0-hf-player-to-2.71.5/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.5.0-hf-player-to-2.71.5"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.5.0-hf-player-to-2.71.5
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.5.0-limit-bulkupload-list/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.5.0-limit-bulkupload-list"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.5.0-limit-bulkupload-list
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.5.0-optimize-acp/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.5.0-optimize-acp"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.5.0-optimize-acp
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.5.0-optimize-getIpFromHttpHeader/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.5.0-optimize-getIpFromHttpHeader"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.5.0-optimize-getIpFromHttpHeader
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.5.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.5.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.5.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.6.0-PLAT-6575/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.6.0-PLAT-6575"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.6.0-PLAT-6575
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.6.0-PLAT-9003/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.6.0-PLAT-9003"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.6.0-PLAT-9003
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.6.0-PLAT-9157/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.6.0-PLAT-9157"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.6.0-PLAT-9157
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.6.0-PLAT-9168/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.6.0-PLAT-9168"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.6.0-PLAT-9168
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.6.0-PLAT-9171/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.6.0-PLAT-9171"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.6.0-PLAT-9171
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.6.0-PLAT-9190/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.6.0-PLAT-9190"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.6.0-PLAT-9190
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.6.0-PLAT-9214-1/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.6.0-PLAT-9214-1"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.6.0-PLAT-9214-1
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.6.0-PLAT-9214/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.6.0-PLAT-9214"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.6.0-PLAT-9214
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.6.0-PLAT-9227/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.6.0-PLAT-9227"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.6.0-PLAT-9227
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.6.0-PLAT-9233-1/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.6.0-PLAT-9233-1"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.6.0-PLAT-9233-1
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.6.0-PLAT-9233/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.6.0-PLAT-9233"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.6.0-PLAT-9233
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.6.0-PLAT-9235/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.6.0-PLAT-9235"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.6.0-PLAT-9235
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.6.0-PLAT-9255/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.6.0-PLAT-9255"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.6.0-PLAT-9255
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.6.0-PLAT-9262/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.6.0-PLAT-9262"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.6.0-PLAT-9262
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.6.0-PLAT-9276/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.6.0-PLAT-9276"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.6.0-PLAT-9276
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.6.0-PLAT-9309/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.6.0-PLAT-9309"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.6.0-PLAT-9309
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.6.0-PLAT-9318/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.6.0-PLAT-9318"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.6.0-PLAT-9318
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.6.0-SUP-15218/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.6.0-SUP-15218"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.6.0-SUP-15218
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.6.0-SUP-15684/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.6.0-SUP-15684"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.6.0-SUP-15684
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.6.0-SendCommandToPubSubServers/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.6.0-SendCommandToPubSubServers"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.6.0-SendCommandToPubSubServers
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.6.0-Updated-Live-Analytic-version/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.6.0-Updated-Live-Analytic-version"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.6.0-Updated-Live-Analytic-version
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.6.0-disable_kava_for_partner/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.6.0-disable_kava_for_partner"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.6.0-disable_kava_for_partner
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.6.0-playkit-preview/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.6.0-playkit-preview"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.6.0-playkit-preview
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.6.0-upload-token-fix-dc-dump/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.6.0-upload-token-fix-dc-dump"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.6.0-upload-token-fix-dc-dump
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.6.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.6.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.6.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.7.0-ACP-fix-pass-by-ref/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.7.0-ACP-fix-pass-by-ref"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.7.0-ACP-fix-pass-by-ref
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.7.0-KMS-18597/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.7.0-KMS-18597"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.7.0-KMS-18597
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.7.0-PLAT-9309/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.7.0-PLAT-9309"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.7.0-PLAT-9309
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.7.0-PLAT-9340-2/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.7.0-PLAT-9340-2"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.7.0-PLAT-9340-2
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.7.0-PLAT-9340-3/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.7.0-PLAT-9340-3"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.7.0-PLAT-9340-3
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.7.0-PLAT-9340-4/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.7.0-PLAT-9340-4"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.7.0-PLAT-9340-4
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.7.0-PLAT-9340/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.7.0-PLAT-9340"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.7.0-PLAT-9340
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.7.0-PLAT-9344/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.7.0-PLAT-9344"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.7.0-PLAT-9344
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.7.0-PLAT-9345/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.7.0-PLAT-9345"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.7.0-PLAT-9345
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.7.0-PLAT-9346/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.7.0-PLAT-9346"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.7.0-PLAT-9346
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.7.0-PLAT-9347/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.7.0-PLAT-9347"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.7.0-PLAT-9347
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.7.0-PLAT-9348/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.7.0-PLAT-9348"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.7.0-PLAT-9348
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.7.0-PLAT-9352/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.7.0-PLAT-9352"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.7.0-PLAT-9352
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.7.0-SUP-15772-update-live-analytic/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.7.0-SUP-15772-update-live-analytic"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.7.0-SUP-15772-update-live-analytic
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.7.0-SUP-15867/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.7.0-SUP-15867"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.7.0-SUP-15867
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.7.0-WEBC-1224_1/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.7.0-WEBC-1224_1"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.7.0-WEBC-1224_1
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.7.0-reduce-access-to-apc/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.7.0-reduce-access-to-apc"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.7.0-reduce-access-to-apc
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.7.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.7.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.7.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.8.0-AN-47/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.8.0-AN-47"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.8.0-AN-47
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.8.0-AN-64/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.8.0-AN-64"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.8.0-AN-64
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.8.0-PLAT-9241/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.8.0-PLAT-9241"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.8.0-PLAT-9241
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.8.0-PLAT-9363-Override-flavorParams-settings/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.8.0-PLAT-9363-Override-flavorParams-settings"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.8.0-PLAT-9363-Override-flavorParams-settings
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.8.0-PLAT-9378_script_for_existing_partners/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.8.0-PLAT-9378_script_for_existing_partners"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.8.0-PLAT-9378_script_for_existing_partners
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.8.0-PLAT-9378/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.8.0-PLAT-9378"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.8.0-PLAT-9378
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.8.0-PLAT-9393/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.8.0-PLAT-9393"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.8.0-PLAT-9393
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.8.0-PLAT-9401/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.8.0-PLAT-9401"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.8.0-PLAT-9401
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.8.0-SUP-15957/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.8.0-SUP-15957"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.8.0-SUP-15957
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.8.0-SUP-16136/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.8.0-SUP-16136"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.8.0-SUP-16136
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.8.0-fixDoccomment/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.8.0-fixDoccomment"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.8.0-fixDoccomment
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.8.0-fixkMrssManager-warning/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.8.0-fixkMrssManager-warning"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.8.0-fixkMrssManager-warning
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.8.0-kmcng-v5.5.1/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.8.0-kmcng-v5.5.1"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.8.0-kmcng-v5.5.1
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.8.0-kmcng-v5.5.2/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.8.0-kmcng-v5.5.2"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.8.0-kmcng-v5.5.2
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.8.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.8.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.8.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.9.0-AN-49/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.9.0-AN-49"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.9.0-AN-49
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.9.0-AN-108/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.9.0-AN-108"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.9.0-AN-108
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.9.0-HandlePushNotificationInstall/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.9.0-HandlePushNotificationInstall"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.9.0-HandlePushNotificationInstall
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.9.0-PLAT-9406_v2/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.9.0-PLAT-9406_v2"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.9.0-PLAT-9406_v2
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.9.0-PLAT-9460-CE-with-EaR-sources/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.9.0-PLAT-9460-CE-with-EaR-sources"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.9.0-PLAT-9460-CE-with-EaR-sources
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.9.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.9.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.9.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.10.0-AN-156/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.10.0-AN-156"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.10.0-AN-156
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.10.0-AN-158/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.10.0-AN-158"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.10.0-AN-158
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.10.0-PLAT-8912/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.10.0-PLAT-8912"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.10.0-PLAT-8912
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.10.0-PLAT-9503/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.10.0-PLAT-9503"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.10.0-PLAT-9503
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.10.0-PLAT-9507/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.10.0-PLAT-9507"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.10.0-PLAT-9507
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.10.0-REACH-CloningTasks/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.10.0-REACH-CloningTasks"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.10.0-REACH-CloningTasks
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.10.0-REACH2-477/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.10.0-REACH2-477"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.10.0-REACH2-477
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Naos-14.10.0-SUP-16219/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.10.0-SUP-16219"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.10.0-SUP-16219
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open selected"
               href="/kaltura/server/blob/Naos-14.10.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Naos-14.10.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Naos-14.10.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/P-222801_Velocix_VOD/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="P-222801_Velocix_VOD"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                P-222801_Velocix_VOD
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/PLAT-8310-add-cfg/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="PLAT-8310-add-cfg"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                PLAT-8310-add-cfg
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/PLAT-8310-studio-v3-integration/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="PLAT-8310-studio-v3-integration"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                PLAT-8310-studio-v3-integration
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/PLAT-9227/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="PLAT-9227"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                PLAT-9227
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/PLAT-9362/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="PLAT-9362"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                PLAT-9362
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/PSVAMB-4593-email-notifications-per-kms-support-php53/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="PSVAMB-4593-email-notifications-per-kms-support-php53"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                PSVAMB-4593-email-notifications-per-kms-support-php53
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/SUP-1957_IX-9.16.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="SUP-1957_IX-9.16.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                SUP-1957_IX-9.16.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/SUP-9349/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="SUP-9349"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                SUP-9349
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/Updateplayerv2.57/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="Updateplayerv2.57"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                Updateplayerv2.57
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/addNewAnalyticsService/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="addNewAnalyticsService"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                addNewAnalyticsService
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/adjust-auto-inc/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="adjust-auto-inc"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                adjust-auto-inc
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-1/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-1"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-1
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-2/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-2"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-2
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-3/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-3"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-3
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-4/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-4"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-4
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-5/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-5"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-5
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-6/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-6"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-6
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-7/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-7"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-7
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-8/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-8"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-8
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-9/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-9"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-9
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-10/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-10"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-10
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-11/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-11"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-11
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-14/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-14"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-14
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-15/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-15"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-15
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-23/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-23"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-23
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-24/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-24"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-24
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-25/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-25"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-25
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-26/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-26"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-26
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-27/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-27"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-27
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-34/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-34"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-34
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-35/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-35"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-35
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-36/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-36"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-36
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-37/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-37"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-37
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-38/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-38"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-38
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-49/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-49"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-49
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-50/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-50"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-50
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-57/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-57"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-57
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-58/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-58"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-58
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-59/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-59"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-59
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-60/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-60"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-60
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-61/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-61"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-61
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-66/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-66"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-66
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-67/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-67"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-67
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-68/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-68"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-68
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-80/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-80"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-80
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-81/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-81"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-81
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-82/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-82"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-82
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-84/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-84"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-84
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-85/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-85"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-85
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-86/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-86"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-86
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-87/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-87"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-87
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-94/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-94"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-94
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-102/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-102"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-102
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-103/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-103"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-103
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-104/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-104"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-104
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-105/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-105"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-105
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-106/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-106"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-106
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-116/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-116"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-116
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-117/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-117"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-117
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-118/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-118"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-118
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-119/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-119"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-119
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-120/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-120"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-120
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-127/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-127"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-127
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-128/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-128"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-128
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-129/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-129"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-129
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-134/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-134"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-134
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/anatolkaltura-patch-142/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="anatolkaltura-patch-142"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                anatolkaltura-patch-142
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/csharp-file-upload-buffer-size/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="csharp-file-upload-buffer-size"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                csharp-file-upload-buffer-size
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/dont-create-kdp-players/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="dont-create-kdp-players"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                dont-create-kdp-players
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/drop-unused-indexes/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="drop-unused-indexes"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                drop-unused-indexes
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/fix-doc-typo/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="fix-doc-typo"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                fix-doc-typo
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/fix-merge-typo/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="fix-merge-typo"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                fix-merge-typo
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/fix-newPassword-input-type/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="fix-newPassword-input-type"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                fix-newPassword-input-type
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/fix-playkit-embed/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="fix-playkit-embed"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                fix-playkit-embed
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/fix-start-page-kmc-admin-console-urls/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="fix-start-page-kmc-admin-console-urls"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                fix-start-page-kmc-admin-console-urls
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/ix--9.16.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="ix--9.16.0"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                ix--9.16.0
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/jupiter-10.2.0-sphinxPlaysViewsScript/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="jupiter-10.2.0-sphinxPlaysViewsScript"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                jupiter-10.2.0-sphinxPlaysViewsScript
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/kmcng-remove-studio-v3/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="kmcng-remove-studio-v3"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                kmcng-remove-studio-v3
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/master/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="master"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                master
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/media/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="media"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                media
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/new-start-page/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="new-start-page"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                new-start-page
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/origin/Mercury-13.2.0-PLAT-7886/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="origin/Mercury-13.2.0-PLAT-7886"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                origin/Mercury-13.2.0-PLAT-7886
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/origin/Mercury-13.5.0-PLAT-8132/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="origin/Mercury-13.5.0-PLAT-8132"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                origin/Mercury-13.5.0-PLAT-8132
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/origin/Mercury-13.5.0-PLAT-8171/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="origin/Mercury-13.5.0-PLAT-8171"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                origin/Mercury-13.5.0-PLAT-8171
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/origin/Mercury-13.7.0-KMS-14832/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="origin/Mercury-13.7.0-KMS-14832"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                origin/Mercury-13.7.0-KMS-14832
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/populate-sphinx-entry-script/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="populate-sphinx-entry-script"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                populate-sphinx-entry-script
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/quiz-refactory-try2/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="quiz-refactory-try2"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                quiz-refactory-try2
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/remove-flix-mencoder-transcoding-engines/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="remove-flix-mencoder-transcoding-engines"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                remove-flix-mencoder-transcoding-engines
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/remove-redundant-alias/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="remove-redundant-alias"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                remove-redundant-alias
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/revert-2991-Jupiter-10.17.0-PLAT-3477/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="revert-2991-Jupiter-10.17.0-PLAT-3477"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                revert-2991-Jupiter-10.17.0-PLAT-3477
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/revert-2995-Jupiter-10.17.0-ReAceTestMe/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="revert-2995-Jupiter-10.17.0-ReAceTestMe"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                revert-2995-Jupiter-10.17.0-ReAceTestMe
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/revert-3063-Jupiter-10.18.0-SUP4380releaseNotes/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="revert-3063-Jupiter-10.18.0-SUP4380releaseNotes"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                revert-3063-Jupiter-10.18.0-SUP4380releaseNotes
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/revert-3203-Jupiter-10.20.0-PLAT-3863/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="revert-3203-Jupiter-10.20.0-PLAT-3863"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                revert-3203-Jupiter-10.20.0-PLAT-3863
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/revert-3344-Kajam-11.0.0-KMS-8680/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="revert-3344-Kajam-11.0.0-KMS-8680"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                revert-3344-Kajam-11.0.0-KMS-8680
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/revert-3904-Kajam-11.10.0-PLAT-4655/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="revert-3904-Kajam-11.10.0-PLAT-4655"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                revert-3904-Kajam-11.10.0-PLAT-4655
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/revert-3927-Kajam-11.10.0-PLAT-5165/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="revert-3927-Kajam-11.10.0-PLAT-5165"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                revert-3927-Kajam-11.10.0-PLAT-5165
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/revert-4091-Kajam-11.13.0-PLAT-5534/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="revert-4091-Kajam-11.13.0-PLAT-5534"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                revert-4091-Kajam-11.13.0-PLAT-5534
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/revert-4330-Kajam-11.16.0-PLAT-5521/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="revert-4330-Kajam-11.16.0-PLAT-5521"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                revert-4330-Kajam-11.16.0-PLAT-5521
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/revert-4490-Kajam-11.19.0-Add-SystemPartner-adminconsole/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="revert-4490-Kajam-11.19.0-Add-SystemPartner-adminconsole"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                revert-4490-Kajam-11.19.0-Add-SystemPartner-adminconsole
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/revert-5182-Lynx-12.9.0-TR-1205/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="revert-5182-Lynx-12.9.0-TR-1205"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                revert-5182-Lynx-12.9.0-TR-1205
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/revert-5206-Lynx-12.10.0-SUP-10147/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="revert-5206-Lynx-12.10.0-SUP-10147"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                revert-5206-Lynx-12.10.0-SUP-10147
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/revert-6016-fix-newPassword-input-type/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="revert-6016-fix-newPassword-input-type"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                revert-6016-fix-newPassword-input-type
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/revert-6021-Mercury-13.2.0-PLAT-7969/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="revert-6021-Mercury-13.2.0-PLAT-7969"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                revert-6021-Mercury-13.2.0-PLAT-7969
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/revert-6022-Mercury-13.2.0-PLAT-7969/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="revert-6022-Mercury-13.2.0-PLAT-7969"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                revert-6022-Mercury-13.2.0-PLAT-7969
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/revert-6277-Mercury-13.7.0-update-version/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="revert-6277-Mercury-13.7.0-update-version"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                revert-6277-Mercury-13.7.0-update-version
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/revert-6550-revert-6466-Mercury-13.8.0-PLAT-8307/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="revert-6550-revert-6466-Mercury-13.8.0-PLAT-8307"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                revert-6550-revert-6466-Mercury-13.8.0-PLAT-8307
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/revert-7224-Mercury-13.20-Reach-creditSyncFix/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="revert-7224-Mercury-13.20-Reach-creditSyncFix"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                revert-7224-Mercury-13.20-Reach-creditSyncFix
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/revert-7537-Naos-14.5.0-PLAT-8932/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="revert-7537-Naos-14.5.0-PLAT-8932"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                revert-7537-Naos-14.5.0-PLAT-8932
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/revert-7687-revert-7537-Naos-14.5.0-PLAT-8932/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="revert-7687-revert-7537-Naos-14.5.0-PLAT-8932"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                revert-7687-revert-7537-Naos-14.5.0-PLAT-8932
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/revert-7750-PLAT-9362/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="revert-7750-PLAT-9362"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                revert-7750-PLAT-9362
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/revert-7797-revert-7750-PLAT-9362/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="revert-7797-revert-7750-PLAT-9362"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                revert-7797-revert-7750-PLAT-9362
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/studio_v2_integration/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="studio_v2_integration"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                studio_v2_integration
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/update-httpd-config/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="update-httpd-config"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                update-httpd-config
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
               href="/kaltura/server/blob/yossipapi-patch-1/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
               data-name="yossipapi-patch-1"
               data-skip-pjax="true"
               rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target js-select-menu-filter-text">
                yossipapi-patch-1
              </span>
            </a>
        </div>

          <div class="select-menu-no-results">Nothing to show</div>
      </div>

      <div class="select-menu-list" role="tabpanel" hidden>
        <div data-filterable-for="context-commitish-filter-field" data-filterable-type="substring">


            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/kajam-11.1.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="kajam-11.1.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="kajam-11.1.0-rel">
                kajam-11.1.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Naos-14.9.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Naos-14.9.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Naos-14.9.0-rel">
                Naos-14.9.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Naos-14.8.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Naos-14.8.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Naos-14.8.0-rel">
                Naos-14.8.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Naos-14.7.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Naos-14.7.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Naos-14.7.0-rel">
                Naos-14.7.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Naos-14.6.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Naos-14.6.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Naos-14.6.0-rel">
                Naos-14.6.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Naos-14.5.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Naos-14.5.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Naos-14.5.0-rel">
                Naos-14.5.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Naos-14.4.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Naos-14.4.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Naos-14.4.0-rel">
                Naos-14.4.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Naos-14.3.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Naos-14.3.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Naos-14.3.0-rel">
                Naos-14.3.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Naos-14.2.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Naos-14.2.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Naos-14.2.0-rel">
                Naos-14.2.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Naos-14.1.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Naos-14.1.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Naos-14.1.0-rel">
                Naos-14.1.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Naos-14.0.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Naos-14.0.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Naos-14.0.0-rel">
                Naos-14.0.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Mercury-13.20.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Mercury-13.20.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Mercury-13.20.0-rel">
                Mercury-13.20.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Mercury-13.19.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Mercury-13.19.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Mercury-13.19.0-rel">
                Mercury-13.19.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Mercury-13.18.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Mercury-13.18.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Mercury-13.18.0-rel">
                Mercury-13.18.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Mercury-13.17.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Mercury-13.17.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Mercury-13.17.0-rel">
                Mercury-13.17.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Mercury-13.16.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Mercury-13.16.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Mercury-13.16.0-rel">
                Mercury-13.16.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Mercury-13.15.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Mercury-13.15.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Mercury-13.15.0-rel">
                Mercury-13.15.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Mercury-13.14.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Mercury-13.14.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Mercury-13.14.0-rel">
                Mercury-13.14.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Mercury-13.13.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Mercury-13.13.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Mercury-13.13.0-rel">
                Mercury-13.13.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Mercury-13.12.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Mercury-13.12.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Mercury-13.12.0-rel">
                Mercury-13.12.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Mercury-13.11.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Mercury-13.11.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Mercury-13.11.0-rel">
                Mercury-13.11.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Mercury-13.10.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Mercury-13.10.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Mercury-13.10.0-rel">
                Mercury-13.10.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Mercury-13.9.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Mercury-13.9.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Mercury-13.9.0-rel">
                Mercury-13.9.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Mercury-13.8.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Mercury-13.8.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Mercury-13.8.0-rel">
                Mercury-13.8.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Mercury-13.7.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Mercury-13.7.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Mercury-13.7.0-rel">
                Mercury-13.7.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Mercury-13.6.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Mercury-13.6.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Mercury-13.6.0-rel">
                Mercury-13.6.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Mercury-13.5.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Mercury-13.5.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Mercury-13.5.0-rel">
                Mercury-13.5.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Mercury-13.4.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Mercury-13.4.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Mercury-13.4.0-rel">
                Mercury-13.4.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Mercury-13.3.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Mercury-13.3.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Mercury-13.3.0-rel">
                Mercury-13.3.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Mercury-13.2.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Mercury-13.2.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Mercury-13.2.0-rel">
                Mercury-13.2.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Mercury-13.1.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Mercury-13.1.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Mercury-13.1.0-rel">
                Mercury-13.1.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Mercury-13.0.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Mercury-13.0.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Mercury-13.0.0-rel">
                Mercury-13.0.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Lynx-12.20.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Lynx-12.20.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Lynx-12.20.0-rel">
                Lynx-12.20.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Lynx-12.19.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Lynx-12.19.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Lynx-12.19.0-rel">
                Lynx-12.19.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Lynx-12.18.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Lynx-12.18.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Lynx-12.18.0-rel">
                Lynx-12.18.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Lynx-12.17.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Lynx-12.17.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Lynx-12.17.0-rel">
                Lynx-12.17.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Lynx-12.16.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Lynx-12.16.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Lynx-12.16.0-rel">
                Lynx-12.16.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Lynx-12.15.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Lynx-12.15.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Lynx-12.15.0-rel">
                Lynx-12.15.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Lynx-12.14.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Lynx-12.14.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Lynx-12.14.0-rel">
                Lynx-12.14.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Lynx-12.13.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Lynx-12.13.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Lynx-12.13.0-rel">
                Lynx-12.13.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Lynx-12.12.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Lynx-12.12.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Lynx-12.12.0-rel">
                Lynx-12.12.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Lynx-12.11.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Lynx-12.11.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Lynx-12.11.0-rel">
                Lynx-12.11.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Lynx-12.10.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Lynx-12.10.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Lynx-12.10.0-rel">
                Lynx-12.10.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Lynx-12.9.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Lynx-12.9.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Lynx-12.9.0-rel">
                Lynx-12.9.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Lynx-12.8.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Lynx-12.8.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Lynx-12.8.0-rel">
                Lynx-12.8.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Lynx-12.7.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Lynx-12.7.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Lynx-12.7.0-rel">
                Lynx-12.7.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Lynx-12.6.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Lynx-12.6.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Lynx-12.6.0-rel">
                Lynx-12.6.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Lynx-12.5.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Lynx-12.5.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Lynx-12.5.0-rel">
                Lynx-12.5.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Lynx-12.4.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Lynx-12.4.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Lynx-12.4.0-rel">
                Lynx-12.4.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Lynx-12.3.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Lynx-12.3.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Lynx-12.3.0-rel">
                Lynx-12.3.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Lynx-12.2.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Lynx-12.2.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Lynx-12.2.0-rel">
                Lynx-12.2.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Lynx-12.1.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Lynx-12.1.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Lynx-12.1.0-rel">
                Lynx-12.1.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Lynx-12.0.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Lynx-12.0.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Lynx-12.0.0-rel">
                Lynx-12.0.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Kajam-11.21.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Kajam-11.21.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Kajam-11.21.0-rel">
                Kajam-11.21.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Kajam-11.20.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Kajam-11.20.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Kajam-11.20.0-rel">
                Kajam-11.20.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Kajam-11.19.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Kajam-11.19.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Kajam-11.19.0-rel">
                Kajam-11.19.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Kajam-11.18.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Kajam-11.18.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Kajam-11.18.0-rel">
                Kajam-11.18.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Kajam-11.17.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Kajam-11.17.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Kajam-11.17.0-rel">
                Kajam-11.17.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Kajam-11.16.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Kajam-11.16.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Kajam-11.16.0-rel">
                Kajam-11.16.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Kajam-11.15.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Kajam-11.15.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Kajam-11.15.0-rel">
                Kajam-11.15.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Kajam-11.14.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Kajam-11.14.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Kajam-11.14.0-rel">
                Kajam-11.14.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Kajam-11.13.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Kajam-11.13.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Kajam-11.13.0-rel">
                Kajam-11.13.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Kajam-11.12.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Kajam-11.12.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Kajam-11.12.0-rel">
                Kajam-11.12.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Kajam-11.11.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Kajam-11.11.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Kajam-11.11.0-rel">
                Kajam-11.11.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Kajam-11.10.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Kajam-11.10.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Kajam-11.10.0-rel">
                Kajam-11.10.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Kajam-11.9.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Kajam-11.9.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Kajam-11.9.0-rel">
                Kajam-11.9.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Kajam-11.8.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Kajam-11.8.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Kajam-11.8.0-rel">
                Kajam-11.8.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Kajam-11.7.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Kajam-11.7.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Kajam-11.7.0-rel">
                Kajam-11.7.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Kajam-11.6.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Kajam-11.6.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Kajam-11.6.0-rel">
                Kajam-11.6.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Kajam-11.5.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Kajam-11.5.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Kajam-11.5.0-rel">
                Kajam-11.5.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Kajam-11.4.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Kajam-11.4.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Kajam-11.4.0-rel">
                Kajam-11.4.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Kajam-11.3.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Kajam-11.3.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Kajam-11.3.0-rel">
                Kajam-11.3.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Kajam-11.2.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Kajam-11.2.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Kajam-11.2.0-rel">
                Kajam-11.2.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Kajam-11.0.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Kajam-11.0.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Kajam-11.0.0-rel">
                Kajam-11.0.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Jupiter-10.21.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Jupiter-10.21.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Jupiter-10.21.0-rel">
                Jupiter-10.21.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Jupiter-10.20.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Jupiter-10.20.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Jupiter-10.20.0-rel">
                Jupiter-10.20.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Jupiter-10.19.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Jupiter-10.19.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Jupiter-10.19.0-rel">
                Jupiter-10.19.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Jupiter-10.18.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Jupiter-10.18.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Jupiter-10.18.0-rel">
                Jupiter-10.18.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Jupiter-10.17.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Jupiter-10.17.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Jupiter-10.17.0-rel">
                Jupiter-10.17.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Jupiter-10.16.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Jupiter-10.16.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Jupiter-10.16.0-rel">
                Jupiter-10.16.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Jupiter-10.15.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Jupiter-10.15.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Jupiter-10.15.0-rel">
                Jupiter-10.15.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Jupiter-10.14.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Jupiter-10.14.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Jupiter-10.14.0-rel">
                Jupiter-10.14.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Jupiter-10.13.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Jupiter-10.13.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Jupiter-10.13.0-rel">
                Jupiter-10.13.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Jupiter-10.12.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Jupiter-10.12.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Jupiter-10.12.0-rel">
                Jupiter-10.12.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Jupiter-10.11.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Jupiter-10.11.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Jupiter-10.11.0-rel">
                Jupiter-10.11.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Jupiter-10.10.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Jupiter-10.10.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Jupiter-10.10.0-rel">
                Jupiter-10.10.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Jupiter-10.9.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Jupiter-10.9.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Jupiter-10.9.0-rel">
                Jupiter-10.9.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Jupiter-10.8.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Jupiter-10.8.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Jupiter-10.8.0-rel">
                Jupiter-10.8.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Jupiter-10.7.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Jupiter-10.7.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Jupiter-10.7.0-rel">
                Jupiter-10.7.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Jupiter-10.6.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Jupiter-10.6.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Jupiter-10.6.0-rel">
                Jupiter-10.6.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Jupiter-10.5.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Jupiter-10.5.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Jupiter-10.5.0-rel">
                Jupiter-10.5.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Jupiter-10.4.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Jupiter-10.4.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Jupiter-10.4.0-rel">
                Jupiter-10.4.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Jupiter-10.3.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Jupiter-10.3.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Jupiter-10.3.0-rel">
                Jupiter-10.3.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Jupiter-10.2.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Jupiter-10.2.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Jupiter-10.2.0-rel">
                Jupiter-10.2.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Jupiter-10.1.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Jupiter-10.1.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Jupiter-10.1.0-rel">
                Jupiter-10.1.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/Jupiter-10.0.0-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="Jupiter-10.0.0-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="Jupiter-10.0.0-rel">
                Jupiter-10.0.0-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/IX-9.19.8-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="IX-9.19.8-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="IX-9.19.8-rel">
                IX-9.19.8-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/IX-9.19.7-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="IX-9.19.7-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="IX-9.19.7-rel">
                IX-9.19.7-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/IX-9.19.6-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="IX-9.19.6-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="IX-9.19.6-rel">
                IX-9.19.6-rel
              </span>
            </a>
            <a class="select-menu-item js-navigation-item js-navigation-open "
              href="/kaltura/server/tree/IX-9.19.5-rel/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh"
              data-name="IX-9.19.5-rel"
              data-skip-pjax="true"
              rel="nofollow">
              <svg class="octicon octicon-check select-menu-item-icon" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M12 5l-8 8-4-4 1.5-1.5L4 10l6.5-6.5L12 5z"/></svg>
              <span class="select-menu-item-text css-truncate-target" title="IX-9.19.5-rel">
                IX-9.19.5-rel
              </span>
            </a>
        </div>

        <div class="select-menu-no-results">Nothing to show</div>
      </div>
      </tab-container>
    </div>
  </div>
</div>

      <div class="BtnGroup float-right">
        <a href="/kaltura/server/find/Naos-14.10.0"
              class="js-pjax-capture-input btn btn-sm BtnGroup-item"
              data-pjax
              data-hotkey="t">
          Find file
        </a>
        <clipboard-copy for="blob-path" class="btn btn-sm BtnGroup-item">
          Copy path
        </clipboard-copy>
      </div>
      <div id="blob-path" class="breadcrumb">
        <span class="repo-root js-repo-root"><span class="js-path-segment"><a data-pjax="true" href="/kaltura/server"><span>server</span></a></span></span><span class="separator">/</span><span class="js-path-segment"><a data-pjax="true" href="/kaltura/server/tree/Naos-14.10.0/plugins"><span>plugins</span></a></span><span class="separator">/</span><span class="js-path-segment"><a data-pjax="true" href="/kaltura/server/tree/Naos-14.10.0/plugins/search"><span>search</span></a></span><span class="separator">/</span><span class="js-path-segment"><a data-pjax="true" href="/kaltura/server/tree/Naos-14.10.0/plugins/search/providers"><span>providers</span></a></span><span class="separator">/</span><span class="js-path-segment"><a data-pjax="true" href="/kaltura/server/tree/Naos-14.10.0/plugins/search/providers/elastic_search"><span>elastic_search</span></a></span><span class="separator">/</span><span class="js-path-segment"><a data-pjax="true" href="/kaltura/server/tree/Naos-14.10.0/plugins/search/providers/elastic_search/scripts"><span>scripts</span></a></span><span class="separator">/</span><strong class="final-path">kaltura_elastic_populate.sh</strong>
      </div>
    </div>


    <include-fragment src="/kaltura/server/contributors/Naos-14.10.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh" class="commit-tease commit-loader">
      <div>
        Fetching contributors&hellip;
      </div>

      <div class="commit-tease-contributors">
          <img alt="" class="loader-loading float-left" src="https://assets-cdn.github.com/images/spinners/octocat-spinner-32-EAF2F5.gif" width="16" height="16" />
        <span class="loader-error">Cannot retrieve contributors at this time</span>
      </div>
</include-fragment>


    <div class="file ">
      <div class="file-header">
  <div class="file-actions">


    <div class="BtnGroup">
      <a id="raw-url" class="btn btn-sm BtnGroup-item" href="/kaltura/server/raw/Naos-14.10.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh">Raw</a>
        <a class="btn btn-sm js-update-url-with-hash BtnGroup-item" data-hotkey="b" href="/kaltura/server/blame/Naos-14.10.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh">Blame</a>
      <a rel="nofollow" class="btn btn-sm BtnGroup-item" href="/kaltura/server/commits/Naos-14.10.0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh">History</a>
    </div>


        <button type="button" class="btn-octicon disabled tooltipped tooltipped-nw"
          aria-label="You must be signed in to make or propose changes">
          <svg class="octicon octicon-pencil" viewBox="0 0 14 16" version="1.1" width="14" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M0 12v3h3l8-8-3-3-8 8zm3 2H1v-2h1v1h1v1zm10.3-9.3L12 6 9 3l1.3-1.3a.996.996 0 0 1 1.41 0l1.59 1.59c.39.39.39 1.02 0 1.41z"/></svg>
        </button>
        <button type="button" class="btn-octicon btn-octicon-danger disabled tooltipped tooltipped-nw"
          aria-label="You must be signed in to make or propose changes">
          <svg class="octicon octicon-trashcan" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M11 2H9c0-.55-.45-1-1-1H5c-.55 0-1 .45-1 1H2c-.55 0-1 .45-1 1v1c0 .55.45 1 1 1v9c0 .55.45 1 1 1h7c.55 0 1-.45 1-1V5c.55 0 1-.45 1-1V3c0-.55-.45-1-1-1zm-1 12H3V5h1v8h1V5h1v8h1V5h1v8h1V5h1v9zm1-10H2V3h9v1z"/></svg>
        </button>
  </div>

  <div class="file-info">
      <span class="file-mode" title="File mode">executable file</span>
      <span class="file-info-divider"></span>
      163 lines (145 sloc)
      <span class="file-info-divider"></span>
    3.18 KB
  </div>
</div>

      

  <div itemprop="text" class="blob-wrapper data type-shell ">
      


<table class="highlight tab-size js-file-line-container" data-tab-size="8">
      <tr>
        <td id="L1" class="blob-num js-line-number" data-line-number="1"></td>
        <td id="LC1" class="blob-code blob-code-inner js-file-line"><span class="pl-c"><span class="pl-c">#!</span>/bin/bash</span></td>
      </tr>
      <tr>
        <td id="L2" class="blob-num js-line-number" data-line-number="2"></td>
        <td id="LC2" class="blob-code blob-code-inner js-file-line"><span class="pl-c1">.</span> /etc/kaltura.d/system.ini</td>
      </tr>
      <tr>
        <td id="L3" class="blob-num js-line-number" data-line-number="3"></td>
        <td id="LC3" class="blob-code blob-code-inner js-file-line">
</td>
      </tr>
      <tr>
        <td id="L4" class="blob-num js-line-number" data-line-number="4"></td>
        <td id="LC4" class="blob-code blob-code-inner js-file-line"><span class="pl-c1">echo</span> <span class="pl-s"><span class="pl-pds">`</span>date<span class="pl-pds">`</span></span></td>
      </tr>
      <tr>
        <td id="L5" class="blob-num js-line-number" data-line-number="5"></td>
        <td id="LC5" class="blob-code blob-code-inner js-file-line">
</td>
      </tr>
      <tr>
        <td id="L6" class="blob-num js-line-number" data-line-number="6"></td>
        <td id="LC6" class="blob-code blob-code-inner js-file-line"><span class="pl-c"><span class="pl-c">#</span></span></td>
      </tr>
      <tr>
        <td id="L7" class="blob-num js-line-number" data-line-number="7"></td>
        <td id="LC7" class="blob-code blob-code-inner js-file-line"><span class="pl-c"><span class="pl-c">#</span> elasticPopulateMgr		This shell script takes care of starting and stopping a Kaltura Elasticsearch Populate Service</span></td>
      </tr>
      <tr>
        <td id="L8" class="blob-num js-line-number" data-line-number="8"></td>
        <td id="LC8" class="blob-code blob-code-inner js-file-line"><span class="pl-c"><span class="pl-c">#</span></span></td>
      </tr>
      <tr>
        <td id="L9" class="blob-num js-line-number" data-line-number="9"></td>
        <td id="LC9" class="blob-code blob-code-inner js-file-line"><span class="pl-c"><span class="pl-c">#</span> chkconfig: 2345 13 87</span></td>
      </tr>
      <tr>
        <td id="L10" class="blob-num js-line-number" data-line-number="10"></td>
        <td id="LC10" class="blob-code blob-code-inner js-file-line"><span class="pl-c"><span class="pl-c">#</span> description: Kaltura Elasticsearch Populate</span></td>
      </tr>
      <tr>
        <td id="L11" class="blob-num js-line-number" data-line-number="11"></td>
        <td id="LC11" class="blob-code blob-code-inner js-file-line">
</td>
      </tr>
      <tr>
        <td id="L12" class="blob-num js-line-number" data-line-number="12"></td>
        <td id="LC12" class="blob-code blob-code-inner js-file-line"><span class="pl-c"><span class="pl-c">#</span>## BEGIN INIT INFO</span></td>
      </tr>
      <tr>
        <td id="L13" class="blob-num js-line-number" data-line-number="13"></td>
        <td id="LC13" class="blob-code blob-code-inner js-file-line"><span class="pl-c"><span class="pl-c">#</span> Provides:          kaltura-elastic-populate</span></td>
      </tr>
      <tr>
        <td id="L14" class="blob-num js-line-number" data-line-number="14"></td>
        <td id="LC14" class="blob-code blob-code-inner js-file-line"><span class="pl-c"><span class="pl-c">#</span> Required-Start:    $local_fs</span></td>
      </tr>
      <tr>
        <td id="L15" class="blob-num js-line-number" data-line-number="15"></td>
        <td id="LC15" class="blob-code blob-code-inner js-file-line"><span class="pl-c"><span class="pl-c">#</span> Required-Stop:     $local_fs</span></td>
      </tr>
      <tr>
        <td id="L16" class="blob-num js-line-number" data-line-number="16"></td>
        <td id="LC16" class="blob-code blob-code-inner js-file-line"><span class="pl-c"><span class="pl-c">#</span> Default-Start:     2 3 4 5</span></td>
      </tr>
      <tr>
        <td id="L17" class="blob-num js-line-number" data-line-number="17"></td>
        <td id="LC17" class="blob-code blob-code-inner js-file-line"><span class="pl-c"><span class="pl-c">#</span> Default-Stop:      0 1 6</span></td>
      </tr>
      <tr>
        <td id="L18" class="blob-num js-line-number" data-line-number="18"></td>
        <td id="LC18" class="blob-code blob-code-inner js-file-line"><span class="pl-c"><span class="pl-c">#</span> X-Interactive:     true</span></td>
      </tr>
      <tr>
        <td id="L19" class="blob-num js-line-number" data-line-number="19"></td>
        <td id="LC19" class="blob-code blob-code-inner js-file-line"><span class="pl-c"><span class="pl-c">#</span> Short-Description: Start/stop Kaltura elastic  populate daemon</span></td>
      </tr>
      <tr>
        <td id="L20" class="blob-num js-line-number" data-line-number="20"></td>
        <td id="LC20" class="blob-code blob-code-inner js-file-line"><span class="pl-c"><span class="pl-c">#</span> Description:       Control the Kaltura elastic populate daemon</span></td>
      </tr>
      <tr>
        <td id="L21" class="blob-num js-line-number" data-line-number="21"></td>
        <td id="LC21" class="blob-code blob-code-inner js-file-line"><span class="pl-c"><span class="pl-c">#</span>## END INIT INFO</span></td>
      </tr>
      <tr>
        <td id="L22" class="blob-num js-line-number" data-line-number="22"></td>
        <td id="LC22" class="blob-code blob-code-inner js-file-line">
</td>
      </tr>
      <tr>
        <td id="L23" class="blob-num js-line-number" data-line-number="23"></td>
        <td id="LC23" class="blob-code blob-code-inner js-file-line"><span class="pl-c"><span class="pl-c">#</span> Source function library.</span></td>
      </tr>
      <tr>
        <td id="L24" class="blob-num js-line-number" data-line-number="24"></td>
        <td id="LC24" class="blob-code blob-code-inner js-file-line"><span class="pl-c"><span class="pl-c">#</span>. /etc/rc.d/init.d/functions</span></td>
      </tr>
      <tr>
        <td id="L25" class="blob-num js-line-number" data-line-number="25"></td>
        <td id="LC25" class="blob-code blob-code-inner js-file-line">
</td>
      </tr>
      <tr>
        <td id="L26" class="blob-num js-line-number" data-line-number="26"></td>
        <td id="LC26" class="blob-code blob-code-inner js-file-line"><span class="pl-c"><span class="pl-c">#</span> Directory containing the populate php files</span></td>
      </tr>
      <tr>
        <td id="L27" class="blob-num js-line-number" data-line-number="27"></td>
        <td id="LC27" class="blob-code blob-code-inner js-file-line">SCRIPTDIR=<span class="pl-smi">$APP_DIR</span>/plugins/search/providers/elastic_search/scripts</td>
      </tr>
      <tr>
        <td id="L28" class="blob-num js-line-number" data-line-number="28"></td>
        <td id="LC28" class="blob-code blob-code-inner js-file-line">
</td>
      </tr>
      <tr>
        <td id="L29" class="blob-num js-line-number" data-line-number="29"></td>
        <td id="LC29" class="blob-code blob-code-inner js-file-line">SCRIPTEXE=populateElasticFromLog.php</td>
      </tr>
      <tr>
        <td id="L30" class="blob-num js-line-number" data-line-number="30"></td>
        <td id="LC30" class="blob-code blob-code-inner js-file-line">
</td>
      </tr>
      <tr>
        <td id="L31" class="blob-num js-line-number" data-line-number="31"></td>
        <td id="LC31" class="blob-code blob-code-inner js-file-line"><span class="pl-k">if</span> [ <span class="pl-smi">$#</span> <span class="pl-k">-ne</span> 1 ]<span class="pl-k">;</span> <span class="pl-k">then</span></td>
      </tr>
      <tr>
        <td id="L32" class="blob-num js-line-number" data-line-number="32"></td>
        <td id="LC32" class="blob-code blob-code-inner js-file-line">	<span class="pl-c1">echo</span> <span class="pl-s"><span class="pl-pds">&quot;</span>Usage: <span class="pl-smi">$0</span> [start|stop|restart|status|forcestart]<span class="pl-pds">&quot;</span></span></td>
      </tr>
      <tr>
        <td id="L33" class="blob-num js-line-number" data-line-number="33"></td>
        <td id="LC33" class="blob-code blob-code-inner js-file-line">	<span class="pl-c1">exit</span> 1</td>
      </tr>
      <tr>
        <td id="L34" class="blob-num js-line-number" data-line-number="34"></td>
        <td id="LC34" class="blob-code blob-code-inner js-file-line"><span class="pl-k">fi</span></td>
      </tr>
      <tr>
        <td id="L35" class="blob-num js-line-number" data-line-number="35"></td>
        <td id="LC35" class="blob-code blob-code-inner js-file-line">
</td>
      </tr>
      <tr>
        <td id="L36" class="blob-num js-line-number" data-line-number="36"></td>
        <td id="LC36" class="blob-code blob-code-inner js-file-line">LOCKFILE=<span class="pl-s"><span class="pl-pds">&quot;</span><span class="pl-smi">$LOG_DIR</span>/populate_elastic.pid<span class="pl-pds">&quot;</span></span></td>
      </tr>
      <tr>
        <td id="L37" class="blob-num js-line-number" data-line-number="37"></td>
        <td id="LC37" class="blob-code blob-code-inner js-file-line">
</td>
      </tr>
      <tr>
        <td id="L38" class="blob-num js-line-number" data-line-number="38"></td>
        <td id="LC38" class="blob-code blob-code-inner js-file-line"><span class="pl-en">echo_success</span>() {</td>
      </tr>
      <tr>
        <td id="L39" class="blob-num js-line-number" data-line-number="39"></td>
        <td id="LC39" class="blob-code blob-code-inner js-file-line">	[ <span class="pl-s"><span class="pl-pds">&quot;</span><span class="pl-smi">$BOOTUP</span><span class="pl-pds">&quot;</span></span> <span class="pl-k">=</span> <span class="pl-s"><span class="pl-pds">&quot;</span>color<span class="pl-pds">&quot;</span></span> ] <span class="pl-k">&amp;&amp;</span> <span class="pl-smi">$MOVE_TO_COL</span></td>
      </tr>
      <tr>
        <td id="L40" class="blob-num js-line-number" data-line-number="40"></td>
        <td id="LC40" class="blob-code blob-code-inner js-file-line">	<span class="pl-c1">echo</span> -n <span class="pl-s"><span class="pl-pds">&quot;</span>[<span class="pl-pds">&quot;</span></span></td>
      </tr>
      <tr>
        <td id="L41" class="blob-num js-line-number" data-line-number="41"></td>
        <td id="LC41" class="blob-code blob-code-inner js-file-line">	[ <span class="pl-s"><span class="pl-pds">&quot;</span><span class="pl-smi">$BOOTUP</span><span class="pl-pds">&quot;</span></span> <span class="pl-k">=</span> <span class="pl-s"><span class="pl-pds">&quot;</span>color<span class="pl-pds">&quot;</span></span> ] <span class="pl-k">&amp;&amp;</span> <span class="pl-smi">$SETCOLOR_SUCCESS</span></td>
      </tr>
      <tr>
        <td id="L42" class="blob-num js-line-number" data-line-number="42"></td>
        <td id="LC42" class="blob-code blob-code-inner js-file-line">	<span class="pl-c1">echo</span> -n <span class="pl-s"><span class="pl-pds">$&quot;</span>	OK	<span class="pl-pds">&quot;</span></span></td>
      </tr>
      <tr>
        <td id="L43" class="blob-num js-line-number" data-line-number="43"></td>
        <td id="LC43" class="blob-code blob-code-inner js-file-line">	[ <span class="pl-s"><span class="pl-pds">&quot;</span><span class="pl-smi">$BOOTUP</span><span class="pl-pds">&quot;</span></span> <span class="pl-k">=</span> <span class="pl-s"><span class="pl-pds">&quot;</span>color<span class="pl-pds">&quot;</span></span> ] <span class="pl-k">&amp;&amp;</span> <span class="pl-smi">$SETCOLOR_NORMAL</span></td>
      </tr>
      <tr>
        <td id="L44" class="blob-num js-line-number" data-line-number="44"></td>
        <td id="LC44" class="blob-code blob-code-inner js-file-line">	<span class="pl-c1">echo</span> -n <span class="pl-s"><span class="pl-pds">&quot;</span>]<span class="pl-pds">&quot;</span></span></td>
      </tr>
      <tr>
        <td id="L45" class="blob-num js-line-number" data-line-number="45"></td>
        <td id="LC45" class="blob-code blob-code-inner js-file-line">	<span class="pl-c1">echo</span> -ne <span class="pl-s"><span class="pl-pds">&quot;</span>\r<span class="pl-pds">&quot;</span></span></td>
      </tr>
      <tr>
        <td id="L46" class="blob-num js-line-number" data-line-number="46"></td>
        <td id="LC46" class="blob-code blob-code-inner js-file-line">	<span class="pl-k">return</span> 0</td>
      </tr>
      <tr>
        <td id="L47" class="blob-num js-line-number" data-line-number="47"></td>
        <td id="LC47" class="blob-code blob-code-inner js-file-line">}</td>
      </tr>
      <tr>
        <td id="L48" class="blob-num js-line-number" data-line-number="48"></td>
        <td id="LC48" class="blob-code blob-code-inner js-file-line">
</td>
      </tr>
      <tr>
        <td id="L49" class="blob-num js-line-number" data-line-number="49"></td>
        <td id="LC49" class="blob-code blob-code-inner js-file-line"><span class="pl-en">echo_failure</span>() {</td>
      </tr>
      <tr>
        <td id="L50" class="blob-num js-line-number" data-line-number="50"></td>
        <td id="LC50" class="blob-code blob-code-inner js-file-line">	[ <span class="pl-s"><span class="pl-pds">&quot;</span><span class="pl-smi">$BOOTUP</span><span class="pl-pds">&quot;</span></span> <span class="pl-k">=</span> <span class="pl-s"><span class="pl-pds">&quot;</span>color<span class="pl-pds">&quot;</span></span> ] <span class="pl-k">&amp;&amp;</span> <span class="pl-smi">$MOVE_TO_COL</span></td>
      </tr>
      <tr>
        <td id="L51" class="blob-num js-line-number" data-line-number="51"></td>
        <td id="LC51" class="blob-code blob-code-inner js-file-line">	<span class="pl-c1">echo</span> -n <span class="pl-s"><span class="pl-pds">&quot;</span>[<span class="pl-pds">&quot;</span></span></td>
      </tr>
      <tr>
        <td id="L52" class="blob-num js-line-number" data-line-number="52"></td>
        <td id="LC52" class="blob-code blob-code-inner js-file-line">	[ <span class="pl-s"><span class="pl-pds">&quot;</span><span class="pl-smi">$BOOTUP</span><span class="pl-pds">&quot;</span></span> <span class="pl-k">=</span> <span class="pl-s"><span class="pl-pds">&quot;</span>color<span class="pl-pds">&quot;</span></span> ] <span class="pl-k">&amp;&amp;</span> <span class="pl-smi">$SETCOLOR_FAILURE</span></td>
      </tr>
      <tr>
        <td id="L53" class="blob-num js-line-number" data-line-number="53"></td>
        <td id="LC53" class="blob-code blob-code-inner js-file-line">	<span class="pl-c1">echo</span> -n <span class="pl-s"><span class="pl-pds">$&quot;</span>FAILED<span class="pl-pds">&quot;</span></span></td>
      </tr>
      <tr>
        <td id="L54" class="blob-num js-line-number" data-line-number="54"></td>
        <td id="LC54" class="blob-code blob-code-inner js-file-line">	[ <span class="pl-s"><span class="pl-pds">&quot;</span><span class="pl-smi">$BOOTUP</span><span class="pl-pds">&quot;</span></span> <span class="pl-k">=</span> <span class="pl-s"><span class="pl-pds">&quot;</span>color<span class="pl-pds">&quot;</span></span> ] <span class="pl-k">&amp;&amp;</span> <span class="pl-smi">$SETCOLOR_NORMAL</span></td>
      </tr>
      <tr>
        <td id="L55" class="blob-num js-line-number" data-line-number="55"></td>
        <td id="LC55" class="blob-code blob-code-inner js-file-line">	<span class="pl-c1">echo</span> -n <span class="pl-s"><span class="pl-pds">&quot;</span>]<span class="pl-pds">&quot;</span></span></td>
      </tr>
      <tr>
        <td id="L56" class="blob-num js-line-number" data-line-number="56"></td>
        <td id="LC56" class="blob-code blob-code-inner js-file-line">	<span class="pl-c1">echo</span> -ne <span class="pl-s"><span class="pl-pds">&quot;</span>\r<span class="pl-pds">&quot;</span></span></td>
      </tr>
      <tr>
        <td id="L57" class="blob-num js-line-number" data-line-number="57"></td>
        <td id="LC57" class="blob-code blob-code-inner js-file-line">	<span class="pl-k">return</span> 0</td>
      </tr>
      <tr>
        <td id="L58" class="blob-num js-line-number" data-line-number="58"></td>
        <td id="LC58" class="blob-code blob-code-inner js-file-line">}</td>
      </tr>
      <tr>
        <td id="L59" class="blob-num js-line-number" data-line-number="59"></td>
        <td id="LC59" class="blob-code blob-code-inner js-file-line">
</td>
      </tr>
      <tr>
        <td id="L60" class="blob-num js-line-number" data-line-number="60"></td>
        <td id="LC60" class="blob-code blob-code-inner js-file-line">
</td>
      </tr>
      <tr>
        <td id="L61" class="blob-num js-line-number" data-line-number="61"></td>
        <td id="LC61" class="blob-code blob-code-inner js-file-line"><span class="pl-en">start</span>() {</td>
      </tr>
      <tr>
        <td id="L62" class="blob-num js-line-number" data-line-number="62"></td>
        <td id="LC62" class="blob-code blob-code-inner js-file-line">	<span class="pl-k">if</span> [ <span class="pl-k">-f</span> <span class="pl-smi">$BASE_DIR</span>/maintenance ] <span class="pl-k">&amp;&amp;</span> [ <span class="pl-s"><span class="pl-pds">&quot;</span>X<span class="pl-smi">$FORCESTART</span><span class="pl-pds">&quot;</span></span> <span class="pl-k">==</span> <span class="pl-s"><span class="pl-pds">&quot;</span>X<span class="pl-pds">&quot;</span></span> ]<span class="pl-k">;</span> <span class="pl-k">then</span></td>
      </tr>
      <tr>
        <td id="L63" class="blob-num js-line-number" data-line-number="63"></td>
        <td id="LC63" class="blob-code blob-code-inner js-file-line">		<span class="pl-c1">echo</span> <span class="pl-s"><span class="pl-pds">&quot;</span>Server is on maintenance mode - elasticPopulateMgr will not start!<span class="pl-pds">&quot;</span></span></td>
      </tr>
      <tr>
        <td id="L64" class="blob-num js-line-number" data-line-number="64"></td>
        <td id="LC64" class="blob-code blob-code-inner js-file-line">		<span class="pl-c1">exit</span> 1</td>
      </tr>
      <tr>
        <td id="L65" class="blob-num js-line-number" data-line-number="65"></td>
        <td id="LC65" class="blob-code blob-code-inner js-file-line">	<span class="pl-k">fi</span></td>
      </tr>
      <tr>
        <td id="L66" class="blob-num js-line-number" data-line-number="66"></td>
        <td id="LC66" class="blob-code blob-code-inner js-file-line">
</td>
      </tr>
      <tr>
        <td id="L67" class="blob-num js-line-number" data-line-number="67"></td>
        <td id="LC67" class="blob-code blob-code-inner js-file-line">	<span class="pl-c1">echo</span> -n <span class="pl-s"><span class="pl-pds">$&quot;</span>Starting:<span class="pl-pds">&quot;</span></span></td>
      </tr>
      <tr>
        <td id="L68" class="blob-num js-line-number" data-line-number="68"></td>
        <td id="LC68" class="blob-code blob-code-inner js-file-line">	KP=<span class="pl-s"><span class="pl-pds">$(</span>pgrep -P 1 -f <span class="pl-smi">$SCRIPTEXE</span><span class="pl-pds">)</span></span></td>
      </tr>
      <tr>
        <td id="L69" class="blob-num js-line-number" data-line-number="69"></td>
        <td id="LC69" class="blob-code blob-code-inner js-file-line">	<span class="pl-k">if</span> <span class="pl-k">!</span> <span class="pl-c1">kill</span> -0 <span class="pl-s"><span class="pl-pds">`</span>cat <span class="pl-smi">$LOCKFILE</span> <span class="pl-k">2&gt;</span>/dev/null<span class="pl-pds">`</span></span> <span class="pl-k">2&gt;</span>/dev/null<span class="pl-k">;</span> <span class="pl-k">then</span></td>
      </tr>
      <tr>
        <td id="L70" class="blob-num js-line-number" data-line-number="70"></td>
        <td id="LC70" class="blob-code blob-code-inner js-file-line">		echo_failure</td>
      </tr>
      <tr>
        <td id="L71" class="blob-num js-line-number" data-line-number="71"></td>
        <td id="LC71" class="blob-code blob-code-inner js-file-line">		<span class="pl-c1">echo</span></td>
      </tr>
      <tr>
        <td id="L72" class="blob-num js-line-number" data-line-number="72"></td>
        <td id="LC72" class="blob-code blob-code-inner js-file-line">		<span class="pl-k">if</span> [ <span class="pl-s"><span class="pl-pds">&quot;</span>X<span class="pl-smi">$KP</span><span class="pl-pds">&quot;</span></span> <span class="pl-k">!=</span> <span class="pl-s"><span class="pl-pds">&quot;</span>X<span class="pl-pds">&quot;</span></span> ]<span class="pl-k">;</span> <span class="pl-k">then</span></td>
      </tr>
      <tr>
        <td id="L73" class="blob-num js-line-number" data-line-number="73"></td>
        <td id="LC73" class="blob-code blob-code-inner js-file-line">			<span class="pl-c1">echo</span> <span class="pl-s"><span class="pl-pds">&quot;</span>Service elastic-populate already running<span class="pl-pds">&quot;</span></span></td>
      </tr>
      <tr>
        <td id="L74" class="blob-num js-line-number" data-line-number="74"></td>
        <td id="LC74" class="blob-code blob-code-inner js-file-line">			<span class="pl-k">return</span> 0</td>
      </tr>
      <tr>
        <td id="L75" class="blob-num js-line-number" data-line-number="75"></td>
        <td id="LC75" class="blob-code blob-code-inner js-file-line">		<span class="pl-k">else</span></td>
      </tr>
      <tr>
        <td id="L76" class="blob-num js-line-number" data-line-number="76"></td>
        <td id="LC76" class="blob-code blob-code-inner js-file-line">			<span class="pl-c1">echo</span> <span class="pl-s"><span class="pl-pds">&quot;</span>Service elastic-populate isn&#39;t running but stale lock file exists<span class="pl-pds">&quot;</span></span></td>
      </tr>
      <tr>
        <td id="L77" class="blob-num js-line-number" data-line-number="77"></td>
        <td id="LC77" class="blob-code blob-code-inner js-file-line">			<span class="pl-c1">echo</span> <span class="pl-s"><span class="pl-pds">&quot;</span>Removing stale lock file at <span class="pl-smi">$LOCKFILE</span><span class="pl-pds">&quot;</span></span></td>
      </tr>
      <tr>
        <td id="L78" class="blob-num js-line-number" data-line-number="78"></td>
        <td id="LC78" class="blob-code blob-code-inner js-file-line">			rm -f <span class="pl-smi">$LOCKFILE</span></td>
      </tr>
      <tr>
        <td id="L79" class="blob-num js-line-number" data-line-number="79"></td>
        <td id="LC79" class="blob-code blob-code-inner js-file-line">			start_scheduler</td>
      </tr>
      <tr>
        <td id="L80" class="blob-num js-line-number" data-line-number="80"></td>
        <td id="LC80" class="blob-code blob-code-inner js-file-line">			<span class="pl-k">return</span> 0</td>
      </tr>
      <tr>
        <td id="L81" class="blob-num js-line-number" data-line-number="81"></td>
        <td id="LC81" class="blob-code blob-code-inner js-file-line">		<span class="pl-k">fi</span></td>
      </tr>
      <tr>
        <td id="L82" class="blob-num js-line-number" data-line-number="82"></td>
        <td id="LC82" class="blob-code blob-code-inner js-file-line">	<span class="pl-k">else</span></td>
      </tr>
      <tr>
        <td id="L83" class="blob-num js-line-number" data-line-number="83"></td>
        <td id="LC83" class="blob-code blob-code-inner js-file-line">		<span class="pl-k">if</span> [ <span class="pl-s"><span class="pl-pds">&quot;</span>X<span class="pl-smi">$KP</span><span class="pl-pds">&quot;</span></span> <span class="pl-k">!=</span> <span class="pl-s"><span class="pl-pds">&quot;</span>X<span class="pl-pds">&quot;</span></span> ]<span class="pl-k">;</span> <span class="pl-k">then</span></td>
      </tr>
      <tr>
        <td id="L84" class="blob-num js-line-number" data-line-number="84"></td>
        <td id="LC84" class="blob-code blob-code-inner js-file-line">			<span class="pl-c1">echo</span> <span class="pl-s"><span class="pl-pds">&quot;</span>Elastic-populate is running as <span class="pl-smi">$KP</span> without a <span class="pl-smi">$LOCKFILE</span><span class="pl-pds">&quot;</span></span></td>
      </tr>
      <tr>
        <td id="L85" class="blob-num js-line-number" data-line-number="85"></td>
        <td id="LC85" class="blob-code blob-code-inner js-file-line">			<span class="pl-c1">exit</span> 0</td>
      </tr>
      <tr>
        <td id="L86" class="blob-num js-line-number" data-line-number="86"></td>
        <td id="LC86" class="blob-code blob-code-inner js-file-line">		<span class="pl-k">fi</span></td>
      </tr>
      <tr>
        <td id="L87" class="blob-num js-line-number" data-line-number="87"></td>
        <td id="LC87" class="blob-code blob-code-inner js-file-line">		start_scheduler</td>
      </tr>
      <tr>
        <td id="L88" class="blob-num js-line-number" data-line-number="88"></td>
        <td id="LC88" class="blob-code blob-code-inner js-file-line">		<span class="pl-k">return</span> 0</td>
      </tr>
      <tr>
        <td id="L89" class="blob-num js-line-number" data-line-number="89"></td>
        <td id="LC89" class="blob-code blob-code-inner js-file-line">	<span class="pl-k">fi</span></td>
      </tr>
      <tr>
        <td id="L90" class="blob-num js-line-number" data-line-number="90"></td>
        <td id="LC90" class="blob-code blob-code-inner js-file-line">}</td>
      </tr>
      <tr>
        <td id="L91" class="blob-num js-line-number" data-line-number="91"></td>
        <td id="LC91" class="blob-code blob-code-inner js-file-line">
</td>
      </tr>
      <tr>
        <td id="L92" class="blob-num js-line-number" data-line-number="92"></td>
        <td id="LC92" class="blob-code blob-code-inner js-file-line"><span class="pl-en">start_scheduler</span>() {</td>
      </tr>
      <tr>
        <td id="L93" class="blob-num js-line-number" data-line-number="93"></td>
        <td id="LC93" class="blob-code blob-code-inner js-file-line">	<span class="pl-c1">echo</span> <span class="pl-s"><span class="pl-pds">&quot;</span><span class="pl-smi">$PHP_BIN</span> <span class="pl-smi">$SCRIPTEXE</span> &gt;&gt; <span class="pl-smi">$LOG_DIR</span>/kaltura_elastic_populate.log 2&gt;&amp;1 &amp;<span class="pl-pds">&quot;</span></span></td>
      </tr>
      <tr>
        <td id="L94" class="blob-num js-line-number" data-line-number="94"></td>
        <td id="LC94" class="blob-code blob-code-inner js-file-line">	<span class="pl-c1">cd</span> <span class="pl-smi">$SCRIPTDIR</span></td>
      </tr>
      <tr>
        <td id="L95" class="blob-num js-line-number" data-line-number="95"></td>
        <td id="LC95" class="blob-code blob-code-inner js-file-line">	su <span class="pl-smi">$OS_KALTURA_USER</span> -c <span class="pl-s"><span class="pl-pds">&quot;</span><span class="pl-smi">$PHP_BIN</span> <span class="pl-smi">$SCRIPTEXE</span> &gt;&gt; <span class="pl-smi">$LOG_DIR</span>/kaltura_elastic_populate.log 2&gt;&amp;1 &amp;<span class="pl-pds">&quot;</span></span></td>
      </tr>
      <tr>
        <td id="L96" class="blob-num js-line-number" data-line-number="96"></td>
        <td id="LC96" class="blob-code blob-code-inner js-file-line">	<span class="pl-k">if</span> [ <span class="pl-s"><span class="pl-pds">&quot;</span><span class="pl-smi">$?</span><span class="pl-pds">&quot;</span></span> <span class="pl-k">-eq</span> 0 ]<span class="pl-k">;</span> <span class="pl-k">then</span></td>
      </tr>
      <tr>
        <td id="L97" class="blob-num js-line-number" data-line-number="97"></td>
        <td id="LC97" class="blob-code blob-code-inner js-file-line">		echo_success</td>
      </tr>
      <tr>
        <td id="L98" class="blob-num js-line-number" data-line-number="98"></td>
        <td id="LC98" class="blob-code blob-code-inner js-file-line">		<span class="pl-c1">echo</span></td>
      </tr>
      <tr>
        <td id="L99" class="blob-num js-line-number" data-line-number="99"></td>
        <td id="LC99" class="blob-code blob-code-inner js-file-line">	<span class="pl-k">else</span></td>
      </tr>
      <tr>
        <td id="L100" class="blob-num js-line-number" data-line-number="100"></td>
        <td id="LC100" class="blob-code blob-code-inner js-file-line">		echo_failure</td>
      </tr>
      <tr>
        <td id="L101" class="blob-num js-line-number" data-line-number="101"></td>
        <td id="LC101" class="blob-code blob-code-inner js-file-line">		<span class="pl-c1">echo</span></td>
      </tr>
      <tr>
        <td id="L102" class="blob-num js-line-number" data-line-number="102"></td>
        <td id="LC102" class="blob-code blob-code-inner js-file-line">	<span class="pl-k">fi</span></td>
      </tr>
      <tr>
        <td id="L103" class="blob-num js-line-number" data-line-number="103"></td>
        <td id="LC103" class="blob-code blob-code-inner js-file-line">}</td>
      </tr>
      <tr>
        <td id="L104" class="blob-num js-line-number" data-line-number="104"></td>
        <td id="LC104" class="blob-code blob-code-inner js-file-line">
</td>
      </tr>
      <tr>
        <td id="L105" class="blob-num js-line-number" data-line-number="105"></td>
        <td id="LC105" class="blob-code blob-code-inner js-file-line"><span class="pl-en">show_status</span>() {</td>
      </tr>
      <tr>
        <td id="L106" class="blob-num js-line-number" data-line-number="106"></td>
        <td id="LC106" class="blob-code blob-code-inner js-file-line">	KP=<span class="pl-s"><span class="pl-pds">$(</span>pgrep -P 1 -f <span class="pl-smi">$SCRIPTEXE</span><span class="pl-pds">)</span></span></td>
      </tr>
      <tr>
        <td id="L107" class="blob-num js-line-number" data-line-number="107"></td>
        <td id="LC107" class="blob-code blob-code-inner js-file-line">	<span class="pl-k">if</span> [ <span class="pl-s"><span class="pl-pds">&quot;</span>X<span class="pl-smi">$KP</span><span class="pl-pds">&quot;</span></span> <span class="pl-k">!=</span> <span class="pl-s"><span class="pl-pds">&quot;</span>X<span class="pl-pds">&quot;</span></span> ]<span class="pl-k">;</span> <span class="pl-k">then</span></td>
      </tr>
      <tr>
        <td id="L108" class="blob-num js-line-number" data-line-number="108"></td>
        <td id="LC108" class="blob-code blob-code-inner js-file-line">	<span class="pl-c1">echo</span> <span class="pl-s"><span class="pl-pds">&quot;</span>Elastic-populate is running as <span class="pl-smi">$KP</span> ...<span class="pl-pds">&quot;</span></span></td>
      </tr>
      <tr>
        <td id="L109" class="blob-num js-line-number" data-line-number="109"></td>
        <td id="LC109" class="blob-code blob-code-inner js-file-line">	<span class="pl-k">return</span> 0</td>
      </tr>
      <tr>
        <td id="L110" class="blob-num js-line-number" data-line-number="110"></td>
        <td id="LC110" class="blob-code blob-code-inner js-file-line">	<span class="pl-k">else</span></td>
      </tr>
      <tr>
        <td id="L111" class="blob-num js-line-number" data-line-number="111"></td>
        <td id="LC111" class="blob-code blob-code-inner js-file-line">		<span class="pl-c1">echo</span> <span class="pl-s"><span class="pl-pds">&quot;</span>Service elastic-populate isn&#39;t running<span class="pl-pds">&quot;</span></span></td>
      </tr>
      <tr>
        <td id="L112" class="blob-num js-line-number" data-line-number="112"></td>
        <td id="LC112" class="blob-code blob-code-inner js-file-line">		<span class="pl-k">return</span> 3</td>
      </tr>
      <tr>
        <td id="L113" class="blob-num js-line-number" data-line-number="113"></td>
        <td id="LC113" class="blob-code blob-code-inner js-file-line">	<span class="pl-k">fi</span></td>
      </tr>
      <tr>
        <td id="L114" class="blob-num js-line-number" data-line-number="114"></td>
        <td id="LC114" class="blob-code blob-code-inner js-file-line">}</td>
      </tr>
      <tr>
        <td id="L115" class="blob-num js-line-number" data-line-number="115"></td>
        <td id="LC115" class="blob-code blob-code-inner js-file-line">
</td>
      </tr>
      <tr>
        <td id="L116" class="blob-num js-line-number" data-line-number="116"></td>
        <td id="LC116" class="blob-code blob-code-inner js-file-line"><span class="pl-en">stop</span>() {</td>
      </tr>
      <tr>
        <td id="L117" class="blob-num js-line-number" data-line-number="117"></td>
        <td id="LC117" class="blob-code blob-code-inner js-file-line">	<span class="pl-c1">echo</span> -n <span class="pl-s"><span class="pl-pds">$&quot;</span>Shutting down:<span class="pl-pds">&quot;</span></span></td>
      </tr>
      <tr>
        <td id="L118" class="blob-num js-line-number" data-line-number="118"></td>
        <td id="LC118" class="blob-code blob-code-inner js-file-line">	KP=<span class="pl-s"><span class="pl-pds">`</span>pgrep -P 1 -f <span class="pl-smi">$SCRIPTEXE</span><span class="pl-k">|</span>xargs<span class="pl-pds">`</span></span></td>
      </tr>
      <tr>
        <td id="L119" class="blob-num js-line-number" data-line-number="119"></td>
        <td id="LC119" class="blob-code blob-code-inner js-file-line">	<span class="pl-k">if</span> [ <span class="pl-k">-n</span> <span class="pl-s"><span class="pl-pds">&quot;</span><span class="pl-smi">$KP</span><span class="pl-pds">&quot;</span></span> ]<span class="pl-k">;</span> <span class="pl-k">then</span></td>
      </tr>
      <tr>
        <td id="L120" class="blob-num js-line-number" data-line-number="120"></td>
        <td id="LC120" class="blob-code blob-code-inner js-file-line">		<span class="pl-c"><span class="pl-c">#</span> hack, returnds the PIDS as string and tells kill to kill all at once</span></td>
      </tr>
      <tr>
        <td id="L121" class="blob-num js-line-number" data-line-number="121"></td>
        <td id="LC121" class="blob-code blob-code-inner js-file-line">		<span class="pl-k">for</span> <span class="pl-smi">pid</span> <span class="pl-k">in</span> <span class="pl-s"><span class="pl-pds">&quot;</span><span class="pl-smi">$KP</span><span class="pl-pds">&quot;</span></span></td>
      </tr>
      <tr>
        <td id="L122" class="blob-num js-line-number" data-line-number="122"></td>
        <td id="LC122" class="blob-code blob-code-inner js-file-line">		<span class="pl-k">do</span></td>
      </tr>
      <tr>
        <td id="L123" class="blob-num js-line-number" data-line-number="123"></td>
        <td id="LC123" class="blob-code blob-code-inner js-file-line">			<span class="pl-c1">kill</span> -9 <span class="pl-smi">$pid</span></td>
      </tr>
      <tr>
        <td id="L124" class="blob-num js-line-number" data-line-number="124"></td>
        <td id="LC124" class="blob-code blob-code-inner js-file-line">		<span class="pl-k">done</span></td>
      </tr>
      <tr>
        <td id="L125" class="blob-num js-line-number" data-line-number="125"></td>
        <td id="LC125" class="blob-code blob-code-inner js-file-line">		echo_success</td>
      </tr>
      <tr>
        <td id="L126" class="blob-num js-line-number" data-line-number="126"></td>
        <td id="LC126" class="blob-code blob-code-inner js-file-line">		<span class="pl-c1">echo</span></td>
      </tr>
      <tr>
        <td id="L127" class="blob-num js-line-number" data-line-number="127"></td>
        <td id="LC127" class="blob-code blob-code-inner js-file-line">		RC=0</td>
      </tr>
      <tr>
        <td id="L128" class="blob-num js-line-number" data-line-number="128"></td>
        <td id="LC128" class="blob-code blob-code-inner js-file-line">	<span class="pl-k">else</span></td>
      </tr>
      <tr>
        <td id="L129" class="blob-num js-line-number" data-line-number="129"></td>
        <td id="LC129" class="blob-code blob-code-inner js-file-line">		echo_failure</td>
      </tr>
      <tr>
        <td id="L130" class="blob-num js-line-number" data-line-number="130"></td>
        <td id="LC130" class="blob-code blob-code-inner js-file-line">		<span class="pl-c1">echo</span></td>
      </tr>
      <tr>
        <td id="L131" class="blob-num js-line-number" data-line-number="131"></td>
        <td id="LC131" class="blob-code blob-code-inner js-file-line">		<span class="pl-c1">echo</span> <span class="pl-s"><span class="pl-pds">&quot;</span>Service elastic-populate not running<span class="pl-pds">&quot;</span></span></td>
      </tr>
      <tr>
        <td id="L132" class="blob-num js-line-number" data-line-number="132"></td>
        <td id="LC132" class="blob-code blob-code-inner js-file-line">		RC=0</td>
      </tr>
      <tr>
        <td id="L133" class="blob-num js-line-number" data-line-number="133"></td>
        <td id="LC133" class="blob-code blob-code-inner js-file-line">	<span class="pl-k">fi</span></td>
      </tr>
      <tr>
        <td id="L134" class="blob-num js-line-number" data-line-number="134"></td>
        <td id="LC134" class="blob-code blob-code-inner js-file-line">	rm -f <span class="pl-smi">$LOCKFILE</span></td>
      </tr>
      <tr>
        <td id="L135" class="blob-num js-line-number" data-line-number="135"></td>
        <td id="LC135" class="blob-code blob-code-inner js-file-line">	<span class="pl-k">return</span> <span class="pl-smi">$RC</span></td>
      </tr>
      <tr>
        <td id="L136" class="blob-num js-line-number" data-line-number="136"></td>
        <td id="LC136" class="blob-code blob-code-inner js-file-line">}</td>
      </tr>
      <tr>
        <td id="L137" class="blob-num js-line-number" data-line-number="137"></td>
        <td id="LC137" class="blob-code blob-code-inner js-file-line">
</td>
      </tr>
      <tr>
        <td id="L138" class="blob-num js-line-number" data-line-number="138"></td>
        <td id="LC138" class="blob-code blob-code-inner js-file-line"><span class="pl-k">case</span> <span class="pl-s"><span class="pl-pds">&quot;</span><span class="pl-smi">$1</span><span class="pl-pds">&quot;</span></span> <span class="pl-k">in</span></td>
      </tr>
      <tr>
        <td id="L139" class="blob-num js-line-number" data-line-number="139"></td>
        <td id="LC139" class="blob-code blob-code-inner js-file-line">	start)</td>
      </tr>
      <tr>
        <td id="L140" class="blob-num js-line-number" data-line-number="140"></td>
        <td id="LC140" class="blob-code blob-code-inner js-file-line">		start</td>
      </tr>
      <tr>
        <td id="L141" class="blob-num js-line-number" data-line-number="141"></td>
        <td id="LC141" class="blob-code blob-code-inner js-file-line">		;;</td>
      </tr>
      <tr>
        <td id="L142" class="blob-num js-line-number" data-line-number="142"></td>
        <td id="LC142" class="blob-code blob-code-inner js-file-line">	stop)</td>
      </tr>
      <tr>
        <td id="L143" class="blob-num js-line-number" data-line-number="143"></td>
        <td id="LC143" class="blob-code blob-code-inner js-file-line">		stop</td>
      </tr>
      <tr>
        <td id="L144" class="blob-num js-line-number" data-line-number="144"></td>
        <td id="LC144" class="blob-code blob-code-inner js-file-line">		;;</td>
      </tr>
      <tr>
        <td id="L145" class="blob-num js-line-number" data-line-number="145"></td>
        <td id="LC145" class="blob-code blob-code-inner js-file-line">	status)</td>
      </tr>
      <tr>
        <td id="L146" class="blob-num js-line-number" data-line-number="146"></td>
        <td id="LC146" class="blob-code blob-code-inner js-file-line">		show_status</td>
      </tr>
      <tr>
        <td id="L147" class="blob-num js-line-number" data-line-number="147"></td>
        <td id="LC147" class="blob-code blob-code-inner js-file-line">		;;</td>
      </tr>
      <tr>
        <td id="L148" class="blob-num js-line-number" data-line-number="148"></td>
        <td id="LC148" class="blob-code blob-code-inner js-file-line">	restart)</td>
      </tr>
      <tr>
        <td id="L149" class="blob-num js-line-number" data-line-number="149"></td>
        <td id="LC149" class="blob-code blob-code-inner js-file-line">		stop</td>
      </tr>
      <tr>
        <td id="L150" class="blob-num js-line-number" data-line-number="150"></td>
        <td id="LC150" class="blob-code blob-code-inner js-file-line">		start</td>
      </tr>
      <tr>
        <td id="L151" class="blob-num js-line-number" data-line-number="151"></td>
        <td id="LC151" class="blob-code blob-code-inner js-file-line">		;;</td>
      </tr>
      <tr>
        <td id="L152" class="blob-num js-line-number" data-line-number="152"></td>
        <td id="LC152" class="blob-code blob-code-inner js-file-line">	forcestart)</td>
      </tr>
      <tr>
        <td id="L153" class="blob-num js-line-number" data-line-number="153"></td>
        <td id="LC153" class="blob-code blob-code-inner js-file-line">		FORCESTART=1</td>
      </tr>
      <tr>
        <td id="L154" class="blob-num js-line-number" data-line-number="154"></td>
        <td id="LC154" class="blob-code blob-code-inner js-file-line">		<span class="pl-c1">echo</span> <span class="pl-s"><span class="pl-pds">&quot;</span>Running in force start mode!!!<span class="pl-pds">&quot;</span></span></td>
      </tr>
      <tr>
        <td id="L155" class="blob-num js-line-number" data-line-number="155"></td>
        <td id="LC155" class="blob-code blob-code-inner js-file-line">		start</td>
      </tr>
      <tr>
        <td id="L156" class="blob-num js-line-number" data-line-number="156"></td>
        <td id="LC156" class="blob-code blob-code-inner js-file-line">		;;</td>
      </tr>
      <tr>
        <td id="L157" class="blob-num js-line-number" data-line-number="157"></td>
        <td id="LC157" class="blob-code blob-code-inner js-file-line">	<span class="pl-k">*</span>)</td>
      </tr>
      <tr>
        <td id="L158" class="blob-num js-line-number" data-line-number="158"></td>
        <td id="LC158" class="blob-code blob-code-inner js-file-line">		<span class="pl-c1">echo</span> <span class="pl-s"><span class="pl-pds">&quot;</span>Usage: [start|stop|restart|status|forcestart]<span class="pl-pds">&quot;</span></span></td>
      </tr>
      <tr>
        <td id="L159" class="blob-num js-line-number" data-line-number="159"></td>
        <td id="LC159" class="blob-code blob-code-inner js-file-line">		<span class="pl-c1">exit</span> 0</td>
      </tr>
      <tr>
        <td id="L160" class="blob-num js-line-number" data-line-number="160"></td>
        <td id="LC160" class="blob-code blob-code-inner js-file-line">		;;</td>
      </tr>
      <tr>
        <td id="L161" class="blob-num js-line-number" data-line-number="161"></td>
        <td id="LC161" class="blob-code blob-code-inner js-file-line"><span class="pl-k">esac</span></td>
      </tr>
      <tr>
        <td id="L162" class="blob-num js-line-number" data-line-number="162"></td>
        <td id="LC162" class="blob-code blob-code-inner js-file-line"><span class="pl-c1">exit</span> 0</td>
      </tr>
</table>

  <details class="details-reset details-overlay BlobToolbar position-absolute js-file-line-actions dropdown d-none" aria-hidden="true">
    <summary class="btn-octicon ml-0 px-2 p-0 bg-white border border-gray-dark rounded-1" aria-label="Inline file action toolbar">
      <svg class="octicon octicon-kebab-horizontal" viewBox="0 0 13 16" version="1.1" width="13" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M1.5 9a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zm5 0a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zM13 7.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/></svg>
    </summary>
    <details-menu>
      <ul class="BlobToolbar-dropdown dropdown-menu dropdown-menu-se mt-2">
        <li><clipboard-copy role="menuitem" class="dropdown-item" id="js-copy-lines" style="cursor:pointer;" data-original-text="Copy lines">Copy lines</clipboard-copy></li>
        <li><clipboard-copy role="menuitem" class="dropdown-item" id="js-copy-permalink" style="cursor:pointer;" data-original-text="Copy permalink">Copy permalink</clipboard-copy></li>
        <li><a class="dropdown-item js-update-url-with-hash" id="js-view-git-blame" role="menuitem" href="/kaltura/server/blame/2f7ae81730a9c1aaddee77fe27cab4dbeed621d0/plugins/search/providers/elastic_search/scripts/kaltura_elastic_populate.sh">View git blame</a></li>
          <li><a class="dropdown-item" id="js-new-issue" role="menuitem" href="/kaltura/server/issues/new">Open new issue</a></li>
      </ul>
    </details-menu>
  </details>

  </div>

    </div>

  

  <details class="details-reset details-overlay details-overlay-dark">
    <summary data-hotkey="l" aria-label="Jump to line"></summary>
    <details-dialog class="Box Box--overlay d-flex flex-column anim-fade-in fast linejump" aria-label="Jump to line">
      <!-- '"` --><!-- </textarea></xmp> --></option></form><form class="js-jump-to-line-form Box-body d-flex" action="" accept-charset="UTF-8" method="get"><input name="utf8" type="hidden" value="&#x2713;" />
        <input class="form-control flex-auto mr-3 linejump-input js-jump-to-line-field" type="text" placeholder="Jump to line&hellip;" aria-label="Jump to line" autofocus>
        <button type="submit" class="btn" data-close-dialog>Go</button>
</form>    </details-dialog>
  </details>



  </div>
  <div class="modal-backdrop js-touch-events"></div>
</div>

    </div>
  </div>

  </div>

        
<div class="footer container-lg px-3" role="contentinfo">
  <div class="position-relative d-flex flex-justify-between pt-6 pb-2 mt-6 f6 text-gray border-top border-gray-light ">
    <ul class="list-style-none d-flex flex-wrap ">
      <li class="mr-3">&copy; 2018 <span title="0.33760s from unicorn-74b889c8f6-kmwbx">GitHub</span>, Inc.</li>
        <li class="mr-3"><a data-ga-click="Footer, go to terms, text:terms" href="https://github.com/site/terms">Terms</a></li>
        <li class="mr-3"><a data-ga-click="Footer, go to privacy, text:privacy" href="https://github.com/site/privacy">Privacy</a></li>
        <li class="mr-3"><a href="/security" data-ga-click="Footer, go to security, text:security">Security</a></li>
        <li class="mr-3"><a href="https://status.github.com/" data-ga-click="Footer, go to status, text:status">Status</a></li>
        <li><a data-ga-click="Footer, go to help, text:help" href="https://help.github.com">Help</a></li>
    </ul>

    <a aria-label="Homepage" title="GitHub" class="footer-octicon mr-lg-4" href="https://github.com">
      <svg height="24" class="octicon octicon-mark-github" viewBox="0 0 16 16" version="1.1" width="24" aria-hidden="true"><path fill-rule="evenodd" d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0 0 16 8c0-4.42-3.58-8-8-8z"/></svg>
</a>
   <ul class="list-style-none d-flex flex-wrap ">
        <li class="mr-3"><a data-ga-click="Footer, go to contact, text:contact" href="https://github.com/contact">Contact GitHub</a></li>
        <li class="mr-3"><a href="https://github.com/pricing" data-ga-click="Footer, go to Pricing, text:Pricing">Pricing</a></li>
      <li class="mr-3"><a href="https://developer.github.com" data-ga-click="Footer, go to api, text:api">API</a></li>
      <li class="mr-3"><a href="https://training.github.com" data-ga-click="Footer, go to training, text:training">Training</a></li>
        <li class="mr-3"><a href="https://blog.github.com" data-ga-click="Footer, go to blog, text:blog">Blog</a></li>
        <li><a data-ga-click="Footer, go to about, text:about" href="https://github.com/about">About</a></li>

    </ul>
  </div>
  <div class="d-flex flex-justify-center pb-6">
    <span class="f6 text-gray-light"></span>
  </div>
</div>



  <div id="ajax-error-message" class="ajax-error-message flash flash-error">
    <svg class="octicon octicon-alert" viewBox="0 0 16 16" version="1.1" width="16" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M8.893 1.5c-.183-.31-.52-.5-.887-.5s-.703.19-.886.5L.138 13.499a.98.98 0 0 0 0 1.001c.193.31.53.501.886.501h13.964c.367 0 .704-.19.877-.5a1.03 1.03 0 0 0 .01-1.002L8.893 1.5zm.133 11.497H6.987v-2.003h2.039v2.003zm0-3.004H6.987V5.987h2.039v4.006z"/></svg>
    <button type="button" class="flash-close js-ajax-error-dismiss" aria-label="Dismiss error">
      <svg class="octicon octicon-x" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M7.48 8l3.75 3.75-1.48 1.48L6 9.48l-3.75 3.75-1.48-1.48L4.52 8 .77 4.25l1.48-1.48L6 6.52l3.75-3.75 1.48 1.48L7.48 8z"/></svg>
    </button>
    You can’t perform that action at this time.
  </div>


    <script crossorigin="anonymous" integrity="sha512-WnyO4VoIUwWWQOmFLjYf4UGg/c1z9VlaLN8IMuiI3uMhhl6rejyThRdLPDyePeUPW6N+38OoBMs6AkqcvWALtA==" type="application/javascript" src="https://assets-cdn.github.com/assets/compat-b66b5d97b4442a01f057c74b091c4368.js"></script>
    <script crossorigin="anonymous" integrity="sha512-092+yG9tBLtacCexwGKGjTtkuRfZo0YUa8VrsiKW+Zh/nyA2j7MvftFLeoIor9CGQ9uDFYNIcbFDbbTPw7tV3Q==" type="application/javascript" src="https://assets-cdn.github.com/assets/frameworks-176ef037f2b2ddbb94c6810c7bce4ec9.js"></script>
    
    <script crossorigin="anonymous" async="async" integrity="sha512-W/rwaeot3DL0Ts4UJnt+ecrHRSexnZrzgPzSm2RxIeDyU1rn+I6kEjiTdO69Nto6j/uFVSCmOKt+LUwvvZQudQ==" type="application/javascript" src="https://assets-cdn.github.com/assets/github-bcb89f5b145473f52b2574c2c3b95419.js"></script>
    
    
    
  <div class="js-stale-session-flash stale-session-flash flash flash-warn flash-banner d-none">
    <svg class="octicon octicon-alert" viewBox="0 0 16 16" version="1.1" width="16" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M8.893 1.5c-.183-.31-.52-.5-.887-.5s-.703.19-.886.5L.138 13.499a.98.98 0 0 0 0 1.001c.193.31.53.501.886.501h13.964c.367 0 .704-.19.877-.5a1.03 1.03 0 0 0 .01-1.002L8.893 1.5zm.133 11.497H6.987v-2.003h2.039v2.003zm0-3.004H6.987V5.987h2.039v4.006z"/></svg>
    <span class="signed-in-tab-flash">You signed in with another tab or window. <a href="">Reload</a> to refresh your session.</span>
    <span class="signed-out-tab-flash">You signed out in another tab or window. <a href="">Reload</a> to refresh your session.</span>
  </div>
  <div class="facebox" id="facebox" style="display:none;">
  <div class="facebox-popup">
    <div class="facebox-content" role="dialog" aria-labelledby="facebox-header" aria-describedby="facebox-description">
    </div>
    <button type="button" class="facebox-close js-facebox-close" aria-label="Close modal">
      <svg class="octicon octicon-x" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M7.48 8l3.75 3.75-1.48 1.48L6 9.48l-3.75 3.75-1.48-1.48L4.52 8 .77 4.25l1.48-1.48L6 6.52l3.75-3.75 1.48 1.48L7.48 8z"/></svg>
    </button>
  </div>
</div>

  <template id="site-details-dialog">
  <details class="details-reset details-overlay details-overlay-dark lh-default text-gray-dark" open>
    <summary aria-haspopup="dialog" aria-label="Close dialog"></summary>
    <details-dialog class="Box Box--overlay d-flex flex-column anim-fade-in fast">
      <button class="Box-btn-octicon m-0 btn-octicon position-absolute right-0 top-0" type="button" aria-label="Close dialog" data-close-dialog>
        <svg class="octicon octicon-x" viewBox="0 0 12 16" version="1.1" width="12" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M7.48 8l3.75 3.75-1.48 1.48L6 9.48l-3.75 3.75-1.48-1.48L4.52 8 .77 4.25l1.48-1.48L6 6.52l3.75-3.75 1.48 1.48L7.48 8z"/></svg>
      </button>
      <div class="octocat-spinner my-6 js-details-dialog-spinner"></div>
    </details-dialog>
  </details>
</template>

  <div class="Popover js-hovercard-content position-absolute" style="display: none; outline: none;" tabindex="0">
  <div class="Popover-message Popover-message--bottom-left Popover-message--large Box box-shadow-large" style="width:360px;">
  </div>
</div>

<div id="hovercard-aria-description" class="sr-only">
  Press h to open a hovercard with more details.
</div>


  </body>
</html>

