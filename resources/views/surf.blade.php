<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta id="token" name="token" value="{{ csrf_token() }}">

        <title>SurfApacolypse</title>

        <link href="{{ elixir('css/global.css') }}" rel="stylesheet">
        <link href="{{ elixir('css/surf.css') }}" rel="stylesheet">

        <!-- Scripts -->
        <script type="text/javascript">
            window.session_id = '{{ auth()->user()->id }}';
            window.session_name = '{{ auth()->user()->name }}';
            window.session_coins = {{ auth()->user()->coins }};
        </script>
        <script src="{{ elixir('js/global.js') }}"></script>
        <script src="{{ elixir('js/surfv.js') }}"></script>
        <script src="{{ elixir('js/surf.js') }}"></script>
    </head>

    <body id="app" v-class="bg: !loading">
        <div v-show="loading" class="big-loader">
            <div class="loader">
                <div class="ball"></div>
                <p>LOADING TOOLS</p>
            </div>
        </div>
        <div v-show="!loading" v-el="main" class="wrapper small-footer hidden">
            <div class="top @{{ currentView }}-view">
                <component is="@{{ currentView }}"></component>


                <div class="notifications">
                    <div class="notification" v-repeat="n in notifications"> @{{{ n }}} </div>
                </div>
                <div class="footer-fence"></div>
            </div>

            <div class="bottom">
                <div class="footer">
                    <div class="inner">
                        <div class="left">
                            <chat></chat>
                        </div>
                        <div class="right">
                            <ul class="menu">
                                <li>Home</li>
                                <li v-on="click: navigate('map')">Surf</li>
                                <li v-on="click: navigate('sites')">Sites</li>
                                <li v-on="click: navigate('teams')">Teams</li>
                                <li>Items</li>
                                <li>Shop</li>
                                <li>House</li>
                                <li class="coins"><span class="count">@{{ coins }}</span> COINS</li>
                            </ul>
                            <div class="online">
                                Team Members Online <span>(Coming Soon)</span> | Friends Online <span>(Coming Soon)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>

</html>
