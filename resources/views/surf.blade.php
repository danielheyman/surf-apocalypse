<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta id="token" name="token" value="{{ csrf_token() }}">

        <title>SurfApacolypse</title>

        <link href="{{ elixir('css/global.css') }}" rel="stylesheet">
        <link href="{{ elixir('css/surf.css') }}" rel="stylesheet">
    </head>

    <body>
        <div id="app">
            <div class="loading">Loading Life...</div>
            <div class="wrapper small-footer">
                <div class="top @{{ currentView }}-view">
                    <component is="@{{ currentView }}"></component>

                    <div class="footer-fence"></div>
                </div>

                <div class="bottom">
                    <div class="footer">
                        <div class="inner">
                            <div class="left" id="chat">
                                <chat></chat>
                            </div>
                            <div class="right">
                                <ul class="menu">
                                    <li>Home</li>
                                    <li v-on="click: navigate('map')">Surf</li>
                                    <li v-on="click: navigate('sites')">Sites</li>
                                    <li>Teams</li>
                                    <li>Items</li>
                                    <li>Shop</li>
                                    <li class="coins"><span class="count">1256</span> COINS</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Scripts -->
        <script type="text/javascript">
            window.session_name = '{{ Auth::user()->name }}';
        </script>
        <script src="{{ elixir('js/global.js') }}"></script>
        <script src="{{ elixir('js/surfv.js') }}"></script>
        <script src="{{ elixir('js/surf.js') }}"></script>
    </body>

</html>
