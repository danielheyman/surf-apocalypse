<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta id="token" name="token" value="{{ csrf_token() }}">

        <title>SurfApacolypse</title>

        <link href="{{ elixir('css/global_vendor.css') }}" rel="stylesheet">
        <link href="{{ elixir('css/inner.css') }}" rel="stylesheet">
        <!-- Scripts -->
        <script type="text/javascript">
            window.session_id = '{{ $user->id }}';
            window.session_name = '{{ $user->name }}';
            window.session_coins = {{ $user->coins }};
            window.session_health = {{ $user->health }};
            window.session_equips = '{!! $equips !!}';
            window.unread_pm = '{{ $unreadPm }}';
        </script>
        <script src="{{ elixir('js/global_vendor.js') }}"></script>
        <script src="{{ elixir('js/inner_vendor.js') }}"></script>
        <script src="{{ elixir('js/inner.js') }}"></script>
    </head>

    <body id="app" :class="{'bg': !loading}">
        <div v-show="loading" class="loader-big">
            <div class="loader">
                <div class="ball"></div>
                <p>Loading SurfApocalypse...</p>
            </div>
        </div>
        <div v-show="!loading" v-el:main class="wrapper small-footer hidden">
            <div class="top @{{ currentView }}-view">
                <component :is="currentView"></component>


                <div class="notifications">
                    <div class="notification" v-for="n in notifications"> @{{{ n }}} </div>
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
                                <li @click="navigate('map')">Surf</li>
                                <li @click="navigate('sites')">Sites</li>
                                <li @click="navigate('teams')">Teams</li>
                                <li>Items</li>
                                <li>Equip</li>
                                <li>Shop</li>
                                <li class="coins"><span class="count">@{{ coins }}</span> COINS</li>
                            </ul>
                            <health></health>
                            <div class="online">
                                Team Members Online <span class="count">()</span>
                                | <span :class="{'blink': unreadPm.length}">Messages <span class="count">(@{{ unreadPm.length }})</span></span>
                                 <!-- | Friends Online <span>(Coming Soon)</span> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <span v-for="profile in openProfiles"><profile :user-id.sync="profile.id" :user-name.sync="profile.name" :on-close.sync="closeProfile"></profile></span>
            <equip></equip>
        </div>
    </body>

</html>
