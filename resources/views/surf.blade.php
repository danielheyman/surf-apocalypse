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
            <div class="wrapper">
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

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Scripts -->
        <script src="{{ elixir('js/global.js') }}"></script>
        <script src="{{ elixir('js/surfv.js') }}"></script>
        <script src="{{ elixir('js/surf.js') }}"></script>
    </body>

</html>
