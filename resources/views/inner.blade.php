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
            <div style="right: 5px; position: absolute; z-index: 3; top: 38px;">
                @foreach(['health', 'coins/gold', 'coins/silver'] as $i)
                    <div style="position: relative; margin-top: 10px; background: rgba(0,0,0,.5); border-radius: 5px; padding: 5px; color: #fff; padding-right: 30px; "> 
                        <span style=" line-height: 20px; ">10</span>
                        <div style=" height: 20px; width: 20px; background: url(http://surf.local:8000/img/surf/icons/{{ $i }}.png); background-size: cover; position: absolute; top: 4px; right: 5px; "></div> 
                    </div>
                @endforeach
            </div>
            
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
                                <li @click="navigate('teams')">Teams (5)</li>
                                <li>Equip</li>
                                <li>Stats</li>
                                <li>Shop</li>
                                <li :class="{'blink': unreadPm.length}">PM (@{{ unreadPm.length }})</li>
                            </ul>
                            <div class="health-wrapper" style="display: flex; margin-top: 14px;">
                                <div style="width: 100px; text-align: center; color: #BAA166; line-height: 23px;">LVL 2</div>
                                <div class="health" style="width: 100%;">
                                    <div class="in" style="width: 80%; background: #545834;">60/80 EXP</div>
                                </div>                                
                            </div>
                            <div style="margin-left: 5px; color: #896D46; text-align: center; padding-left: 50px;">
                                accuracy (10) defense (10) stength (20) luck (5) speed (8)
                            </div>
                            <!--<health></health>-->
                            <!--<div class="online">
                                Team Members Online <span class="count">()</span>
                                | <span>Messages <span class="count">()</span></span>
                            </div>-->
                        </div>
                    </div>
                </div>
            </div>

            <span v-for="profile in openProfiles"><profile :user-id.sync="profile.id" :user-name.sync="profile.name" :on-close.sync="closeProfile"></profile></span>
            <!--<equip></equip>-->
        </div>
    </body>

</html>
