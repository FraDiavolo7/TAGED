// MMN Datamining research
console.clear();

var dataset = "";
var game = "{";
var roundNum = 0;
var strokeNum;
var newStroke = false;
var matchNum;
var shape;
var matchLength;
var matchColor;
var timeLeft = 0;
var igt;
var lastIgt;
var specialFour = false;
var beamMatch = false;

var md5 = function(s){function L(k,d){return(k<<d)|(k>>>(32-d))}function K(G,k){var I,d,F,H,x;F=(G&2147483648);H=(k&2147483648);I=(G&1073741824);d=(k&1073741824);x=(G&1073741823)+(k&1073741823);if(I&d){return(x^2147483648^F^H)}if(I|d){if(x&1073741824){return(x^3221225472^F^H)}else{return(x^1073741824^F^H)}}else{return(x^F^H)}}function r(d,F,k){return(d&F)|((~d)&k)}function q(d,F,k){return(d&k)|(F&(~k))}function p(d,F,k){return(d^F^k)}function n(d,F,k){return(F^(d|(~k)))}function u(G,F,aa,Z,k,H,I){G=K(G,K(K(r(F,aa,Z),k),I));return K(L(G,H),F)}function f(G,F,aa,Z,k,H,I){G=K(G,K(K(q(F,aa,Z),k),I));return K(L(G,H),F)}function D(G,F,aa,Z,k,H,I){G=K(G,K(K(p(F,aa,Z),k),I));return K(L(G,H),F)}function t(G,F,aa,Z,k,H,I){G=K(G,K(K(n(F,aa,Z),k),I));return K(L(G,H),F)}function e(G){var Z;var F=G.length;var x=F+8;var k=(x-(x%64))/64;var I=(k+1)*16;var aa=Array(I-1);var d=0;var H=0;while(H<F){Z=(H-(H%4))/4;d=(H%4)*8;aa[Z]=(aa[Z]| (G.charCodeAt(H)<<d));H++}Z=(H-(H%4))/4;d=(H%4)*8;aa[Z]=aa[Z]|(128<<d);aa[I-2]=F<<3;aa[I-1]=F>>>29;return aa}function B(x){var k="",F="",G,d;for(d=0;d<=3;d++){G=(x>>>(d*8))&255;F="0"+G.toString(16);k=k+F.substr(F.length-2,2)}return k}function J(k){k=k.replace(/rn/g,"n");var d="";for(var F=0;F<k.length;F++){var x=k.charCodeAt(F);if(x<128){d+=String.fromCharCode(x)}else{if((x>127)&&(x<2048)){d+=String.fromCharCode((x>>6)|192);d+=String.fromCharCode((x&63)|128)}else{d+=String.fromCharCode((x>>12)|224);d+=String.fromCharCode(((x>>6)&63)|128);d+=String.fromCharCode((x&63)|128)}}}return d}var C=Array();var P,h,E,v,g,Y,X,W,V;var S=7,Q=12,N=17,M=22;var A=5,z=9,y=14,w=20;var o=4,m=11,l=16,j=23;var U=6,T=10,R=15,O=21;s=J(s);C=e(s);Y=1732584193;X=4023233417;W=2562383102;V=271733878;for(P=0;P<C.length;P+=16){h=Y;E=X;v=W;g=V;Y=u(Y,X,W,V,C[P+0],S,3614090360);V=u(V,Y,X,W,C[P+1],Q,3905402710);W=u(W,V,Y,X,C[P+2],N,606105819);X=u(X,W,V,Y,C[P+3],M,3250441966);Y=u(Y,X,W,V,C[P+4],S,4118548399);V=u(V,Y,X,W,C[P+5],Q,1200080426);W=u(W,V,Y,X,C[P+6],N,2821735955);X=u(X,W,V,Y,C[P+7],M,4249261313);Y=u(Y,X,W,V,C[P+8],S,1770035416);V=u(V,Y,X,W,C[P+9],Q,2336552879);W=u(W,V,Y,X,C[P+10],N,4294925233);X=u(X,W,V,Y,C[P+11],M,2304563134);Y=u(Y,X,W,V,C[P+12],S,1804603682);V=u(V,Y,X,W,C[P+13],Q,4254626195);W=u(W,V,Y,X,C[P+14],N,2792965006);X=u(X,W,V,Y,C[P+15],M,1236535329);Y=f(Y,X,W,V,C[P+1],A,4129170786);V=f(V,Y,X,W,C[P+6],z,3225465664);W=f(W,V,Y,X,C[P+11],y,643717713);X=f(X,W,V,Y,C[P+0],w,3921069994);Y=f(Y,X,W,V,C[P+5],A,3593408605);V=f(V,Y,X,W,C[P+10],z,38016083);W=f(W,V,Y,X,C[P+15],y,3634488961);X=f(X,W,V,Y,C[P+4],w,3889429448);Y=f(Y,X,W,V,C[P+9],A,568446438);V=f(V,Y,X,W,C[P+14],z,3275163606);W=f(W,V,Y,X,C[P+3],y,4107603335);X=f(X,W,V,Y,C[P+8],w,1163531501);Y=f(Y,X,W,V,C[P+13],A,2850285829);V=f(V,Y,X,W,C[P+2],z,4243563512);W=f(W,V,Y,X,C[P+7],y,1735328473);X=f(X,W,V,Y,C[P+12],w,2368359562);Y=D(Y,X,W,V,C[P+5],o,4294588738);V=D(V,Y,X,W,C[P+8],m,2272392833);W=D(W,V,Y,X,C[P+11],l,1839030562);X=D(X,W,V,Y,C[P+14],j,4259657740);Y=D(Y,X,W,V,C[P+1],o,2763975236);V=D(V,Y,X,W,C[P+4],m,1272893353);W=D(W,V,Y,X,C[P+7],l,4139469664);X=D(X,W,V,Y,C[P+10],j,3200236656);Y=D(Y,X,W,V,C[P+13],o,681279174);V=D(V,Y,X,W,C[P+0],m,3936430074);W=D(W,V,Y,X,C[P+3],l,3572445317);X=D(X,W,V,Y,C[P+6],j,76029189);Y=D(Y,X,W,V,C[P+9],o,3654602809);V=D(V,Y,X,W,C[P+12],m,3873151461);W=D(W,V,Y,X,C[P+15],l,530742520);X=D(X,W,V,Y,C[P+2],j,3299628645);Y=t(Y,X,W,V,C[P+0],U,4096336452);V=t(V,Y,X,W,C[P+7],T,1126891415);W=t(W,V,Y,X,C[P+14],R,2878612391);X=t(X,W,V,Y,C[P+5],O,4237533241);Y=t(Y,X,W,V,C[P+12],U,1700485571);V=t(V,Y,X,W,C[P+3],T,2399980690);W=t(W,V,Y,X,C[P+10],R,4293915773);X=t(X,W,V,Y,C[P+1],O,2240044497);Y=t(Y,X,W,V,C[P+8],U,1873313359);V=t(V,Y,X,W,C[P+15],T,4264355552);W=t(W,V,Y,X,C[P+6],R,2734768916);X=t(X,W,V,Y,C[P+13],O,1309151649);Y=t(Y,X,W,V,C[P+4],U,4149444226);V=t(V,Y,X,W,C[P+11],T,3174756917);W=t(W,V,Y,X,C[P+2],R,718787259);X=t(X,W,V,Y,C[P+9],O,3951481745);Y=K(Y,h);X=K(X,E);W=K(W,v);V=K(V,g)}var i=B(Y)+B(X)+B(W)+B(V);return i.toLowerCase()};

$.getJSON('http://gd.geobytes.com/GetCityDetails?callback=?', function(data) {
    //console.log(JSON.stringify(data, null, 2));
        
    //console.log(data.geobytesipaddress);
    //console.log(data.geobytescountry);
    //console.log(data.geobytesregion);
    //console.log(data.geobytescity);
    //console.log(data.geobyteslatitude);
    //console.log(data.geobyteslongitude);
    
    var timeTmp = new Date().getTime();
    
    game += "\n    \"player\":\n    {\n";
    game += "        \"player_id\":\"" + md5(data.geobytesipaddress + timeTmp) + "\"";
    game += ",\n        \"ipaddress\":\"" + data.geobytesipaddress + "\"";
    game += ",\n        \"country\":\"" + data.geobytescountry + "\"";
    game += ",\n        \"region\":\"" + data.geobytesregion + "\"";
    game += ",\n        \"city\":\"" + data.geobytescity + "\"";
    game += ",\n        \"latitude\":\"" + data.geobyteslatitude + "\"";
    game += ",\n        \"longitude\":\"" + data.geobyteslongitude + "\"";
    game += "\n    },";
    game += "\n    \"game\":\n    {\n";
    game += "        \"game_date\":\"" + timeTmp + "\"";
    game += "\n    }";
});

function createCookie(name,value,days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + value + expires + "; path=/";
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function eraseCookie(name) {
    createCookie(name,"",-1);
}

//createCookie('ppkcookie','testcookie',7);

//var x = readCookie('ppkcookie')
//if (x) {
//    [do something with x]
//}

// Open Source Match 3 Game by Clockworkchilli
App = function()
{
    var self = this; // Main app context

    // Layers to use for rendering
    this.layers = {background:17, boardBack:16, board:15, boardFront:14, front:13};

    // Flags
    this.musicMuted = false;
    this.soundMuted = false;
    this.socialEnabled = true;
    this.gameOver = false;

    // Scores
    this.scores = {values:[0,0,0]};

    /**
     * Function that takes a number of seconds and returns a string of the time in minutes
     * @param {number} numSeconds The number of seconds that we will convert
     * @returns {string} A string representation of the provided time in minutes
     */
    this.getTimeString = function (numSeconds)
    {
        timeLeft = numSeconds;
        
        if (!numSeconds || numSeconds < 1)
        {
            return '0:00';
        }

        var timeString = '';
        var minutes = 0;
        var seconds = Math.floor(numSeconds);

        // Deal with minutes
        while (seconds >= 60)
        {
            seconds -= 60;
            minutes++;
        }

        timeString = minutes > 0 ? minutes + ':' : '0:';

        // Deal with seconds
        if (seconds > 0)
        {
            timeString += seconds < 10 ? ('0' + seconds) : seconds;
        }
        else
        {
            timeString += '00';
        }
        return timeString;
    };

    // Load all assets
    this.load = function()
    {   
        // LOAD SCRIPTS
        wade.loadScript('bar.js');
        wade.loadScript('counter.js');
        wade.loadScript('match3.js');

        // Load AUDIO
        if (wade.isWebAudioSupported())
        {
            // background music
            wade.preloadAudio('sounds/Surreal-Chase.ogg', false, true);
        }

        if (wade.isWebAudioSupported())
        {
            wade.loadAudio('sounds/metalImpact2.ogg');
            wade.loadAudio('sounds/fiveSound.ogg');
            wade.loadAudio('sounds/explosion1.ogg');
        }

        // LOAD IMAGES
        // Squares
        wade.loadImage('images/red.png');
        wade.loadImage('images/blue.png');
        wade.loadImage('images/green.png');
        wade.loadImage('images/yellow.png');
        wade.loadImage('images/selected.png');
        wade.loadImage('images/special4.png');
        wade.loadImage('images/special5.png');
        wade.loadImage('images/redGlow.png');
        wade.loadImage('images/blueGlow.png');
        wade.loadImage('images/greenGlow.png');
        wade.loadImage('images/yellowGlow.png');

        // UI and background
        wade.loadImage('images/background.png');
        wade.loadImage('images/top.png');
        wade.loadImage('images/barTime.png');
        wade.loadImage('images/markerTime.png');
        wade.loadImage('images/buttonSoundOff.png');
        wade.loadImage('images/buttonSoundOn.png');
        wade.loadImage('images/buttonBack.png');
        wade.loadImage('images/potionBar.png');
        wade.loadImage('images/menuBackground.png');
        wade.loadImage('images/wordTitle.png');
        wade.loadImage('images/potionTitle.png');
        wade.loadImage('images/buttonPlay.png');
        wade.loadImage('images/backgroundShareBox.png');
        wade.loadImage('images/buttonCredit.png');
        wade.loadImage('images/wadePowered.png');
        wade.loadImage('images/buttonsMuteOn.png');
        wade.loadImage('images/buttonsMuteOff.png');
        wade.loadImage('images/buttonPause.png');
        wade.loadImage('images/buttonUnpause.png');

        // Shiny
        wade.loadImage('images/shatter.png');
        wade.loadImage('images/specialEffect1.png');
        wade.loadImage('images/bigBoom.png');
        wade.loadImage('images/fiveEffect.png');
        wade.loadImage('images/flash.png');

        // Share
        wade.loadImage('images/google.png');
        wade.loadImage('images/facebook.png');
        wade.loadImage('images/twitter.png');

    };

    // Enter main program
    this.init = function()
    {
        // Setup screen
        wade.setMinScreenSize(608, 920); //996
        wade.setMaxScreenSize(608, 920); //996

        wade.setSwipeTolerance(1, 2);

        // {background:17, boardBack:16, board:15, boardFront:14, front:13};
        wade.setLayerRenderMode(self.layers.background, "webgl");
        wade.setLayerRenderMode(self.layers.boardBack, "webgl");
        wade.setLayerRenderMode(self.layers.board, "webgl");
        wade.setLayerRenderMode(self.layers.boardFront, "webgl");
        //wade.setLayerRenderMode(self.layers.front, "webgl"); // Need 1 canvas layer for timer bar gradient and other etc

        // Lower resolution factor if mobile
        if (wade.getContainerHeight() <= 768)
        {
            self.isMobile = true;
            wade.setLayerResolutionFactor(this.layers.background, 0.75);
            wade.setLayerResolutionFactor(this.layers.boardBack, 0.75);
            wade.setLayerResolutionFactor(this.layers.board, 0.75);
            wade.setLayerResolutionFactor(this.layers.boardFront, 0.75);
            wade.setLayerResolutionFactor(this.layers.front, 0.75);
        }
        else
        {
            self.isMobile = false;
        }

        // Create main menu and the game on play pressed
        this.game();
    };

    /**
     * Creates the main menu
     */
    this.game = function()
    {
        // MMN Datamining research
        console.log(game);

        // Create menu graphical elements
        var backgroundSprite = new Sprite('images/menuBackground.png', this.layers.boardBack);
        var menu = new SceneObject(backgroundSprite);
        wade.addSceneObject(menu, true);
        var titleSprite = new Sprite('images/wordTitle.png', this.layers.board);
        menu.addSprite(titleSprite, {x: 0, y:-wade.getScreenHeight()/2 + 100});
        var potionSprite = new Sprite('images/potionTitle.png', this.layers.board);
        menu.addSprite(potionSprite, {x:0, y:-130});
        var shareBackSprite = new Sprite('images/backgroundShareBox.png', wade.app.layers.front);
        menu.addSprite(shareBackSprite, {x:-wade.getScreenWidth()/2 + 175, y:wade.getScreenHeight()/2 - 125});

        // Create play button
        var playButtonSprite = new Sprite('images/buttonPlay.png', wade.app.layers.front);
        var playButton = new SceneObject(playButtonSprite);
        playButton.onMouseUp = function()
        {
            // MMN Datamining research
            document.addEventListener('keydown', (event) => {
                //console.log(event.key);
                if (event.ctrlKey && event.altKey && event.key == 'c') {
                    console.log('God Mode: exit');

                    timer.getBehavior().addTime(-1000);
                }
            }, false);

            var timer = new Date();
            var time = timer.getTime();
            var str = "";

            strokeNum = 0;
            igt = 0;
            lastIgt = igt;

            console.log(lastIgt + ", " + igt);

            ++roundNum;
            str += ",\n    \"round\":\n    {\n";
            str += "        \"round_num\":\"" + roundNum + "\"";
            str += ",\n        \"round_date\":\"" + time + "\"";
            
            dataset = game + str;
            
            console.log(str);
            
            wade.clearScene();
            if(!self.musicMuted)
            {
                self.musicPlaying = true;
                self.musicSource = wade.playAudio('sounds/Surreal-Chase.ogg', true);
            }

            // Draw background and foreground
            var backgroundSprite = new Sprite('images/background.png', self.layers.background);
            backgroundSprite.setSize(608, 920);
            var topSprite = new Sprite('images/top.png', self.layers.front);
            var graphics = new SceneObject(null);
            graphics.addSprite(backgroundSprite, {x:0, y:wade.getScreenHeight()/2 - backgroundSprite.getSize().y/2});
            graphics.addSprite(topSprite, {x:0, y:-backgroundSprite.getSize().y/2 + 74}); // Evil magic numbers
            wade.addSceneObject(graphics);

            // Use Match3 behavior to create the game
            this.theGame = new SceneObject(null, Match3);
            wade.addSceneObject(this.theGame, true, {match3:
            {
                numCells: {x:7, y:7},
                cellSize: {x:85, y:85},
                margin: 5,
                items: [{normal: 'images/red.png', special:'images/redGlow.png', probability:25},
                    {normal: 'images/blue.png', special:'images/blueGlow.png', probability:25},
                    {normal: 'images/green.png', special:'images/greenGlow.png', probability:25},
                    {normal: 'images/yellow.png', special:'images/yellowGlow.png', probability:25}],
                specialFive: 'images/special5.png',
                matchSound: 'sounds/metalImpact2.ogg',
                explosionSound: 'sounds/explosion1.ogg',
                specialFiveSound: 'sounds/fiveSound.ogg',
                itemLayer: self.layers.board,
                bottomLayer: self.layers.boardBack,
                topLayer: self.layers.boardFront,
                gravity: 2000,
                effectScale: 1.5,
                sparkleAnimation: {name:'images/specialEffect1.png', numCellsX:5, numCellsY:4, speed:15, looping:false},
                splashAnimation: {name:'images/shatter.png', numCellsX:5, numCellsY:5, speed:60, looping:false},
                explosionAnimation: {name:'images/bigBoom.png', numCellsX:6, numCellsY:4, speed:30, looping:false},
                specialFourAnimation: {name:'images/flash.png', numCellsX:4, numCellsY:3, speed:15, looping:true},
                specialFiveAnimation: {name:'images/fiveEffect.png',numCellsX:5, numCellsY:4, speed:30, looping:false},
                glowSize:16

            }});

            // Create the timer
            var timerBarSprite = new Sprite('images/barTime.png', self.layers.front); //self.layers.front
            var timer = new SceneObject(timerBarSprite, Bar);
            //timer.setSpriteOffsets(timerOffset);
            timer.removeOnGameOver = true;
            timer.timePassed = 0;
            timer.setPosition(0, 330);
            timer.onUpdate = function () {
                timer.timePassed += wade.c_timeStep;
                var percent = (30 - timer.timePassed) / 30 * 100;
                
                // MMN Datamining research
                igt = timer.timePassed;
            };
            wade.addSceneObject(timer, true);
            timer.getBehavior('Bar').init({bar: {size: {x: 580, y: 30},
                timer: 30,
                layer: self.layers.front,
                reverse: true,
                offset: {x:0,y:0},
                spriteIndex: 1,
                useGradient: true,
                foreColor: ['#00FF00', '#FF0000'],
                marker: 'images/markerTime.png',
                markerLayer: self.layers.front}});

            wade.app.onScoreAdded = function(value)
            {
                timer.getBehavior().addTime(value/300);
            };

            self.inGameButtons();

            // Create score text
            var scoreText = new TextSprite('SCORE','64px ArtDept1', 'white', 'center', self.layers.front);
            scoreText.setShadow('#000000', 1, 2, 2);
            var scoreT = new TextSprite('0', '42px Monopower', 'white', 'center', self.layers.front);
            scoreT.setShadow('#000000', 3, 0, 4);
            self.scoreObject = new SceneObject(scoreT, Counter);
            self.scoreObject.removeOnGameOver = true;
            self.scoreObject.setPosition(0, -wade.getScreenHeight()/2 + 138);
            self.scoreObject.addSprite(scoreText, {x:0, y:-65});
            wade.addSceneObject(self.scoreObject);

            // Increment score
            self.onMatch = function(match)
            {
                self.scoreObject.getBehavior().addValue(match.length*100);
                
                // MMN Datamining research
                var str = "";
                
                var timer = new Date();
                var t = (timer.getTime() - time) / 1000;
                
                if (newStroke) {
                    newStroke = false;

                    if (strokeNum == 1) {
                        str +="    ,\n";
                        str += "        \"stroke\":[\n";
                    } else {
                        str += "              ]\n            },\n";
                    }

                    str += "            {\n";
                    str += "            \"stroke_num\":\"" + strokeNum + "\"";
                    str += ",\n            \"duration\":\"" + (Math.round((igt - lastIgt) * 100) / 100) + "\"";
                    str += ",\n            \"time\":\"" + (timer.getTime()) + "\"";
                    str += ",\n            \"match\":[\n";
                    matchNum = 0;
                }
                ++matchNum;

                if (matchNum > 1) {
                    str += ",\n";
                }

                str += "                {\n";
                str += "                \"match_num\":\"" + matchNum + "\"";
                str += ",\n                \"color\":\"" + matchColor + "\"";
                str += ",\n                \"length\":\"" + matchLength + "\"";
                str += ",\n                \"shape\":\"" + shape + "\"";
                str += ",\n                \"score\":\"" + (match.length*100) + "\"";
                str += ",\n                \"score_total\":\"" + (self.scoreObject.getBehavior().getValue()) + "\"";
                
                var specialFourStr = "false";
                if (specialFour) {
                    specialFour = false;
                    specialFourStr = "true";
                }
                str += ",\n                \"special_four\":\"" + specialFourStr + "\"";
                
                var beamStr = "false";
                if (beamMatch) {
                    beamMatch = false;
                    beamStr = "true";
                }
                str += ",\n                \"beam\":\"" + beamStr + "\"";
                
                str += ",\n                \"time\":\"" + (Math.round(t * 100) / 100) + "\"";
                str += ",\n                \"time_left\":\"" + timeLeft + "\"";
                str += ",\n                \"in_game_time\":\"" + (Math.round(igt * 100) / 100) + "\"";
                
                str += "\n                }";
                
                dataset += str;
                
                time = timer.getTime();
                lastIgt = igt;
                
                console.log(str);
            };

        };
        playButton.setPosition(0, 130);
        playButtonSprite.setDrawFunction(wade.drawFunctions.resizeOverTime_ (30, 16, 301, 156, 0.3, playButtonSprite.getDrawFunction(), function()
        {
            // Create credits button
            var creditsButtonSprite = new Sprite('images/buttonCredit.png', self.layers.front);
            var creditsButton = new SceneObject(creditsButtonSprite);
            creditsButtonSprite.setDrawFunction(wade.drawFunctions.fadeOpacity_(0.0, 1.0, 1.0, creditsButtonSprite.getDrawFunction()));
            creditsButton.onMouseUp = function()
            {
                wade.clearScene();
                self.credits();
            };
            creditsButton.setPosition(-wade.getScreenWidth()/2 + 175, wade.getScreenHeight()/2 - 180);
            wade.addSceneObject(creditsButton, true);

            // Create share buttons if social flag set
            if(self.socialEnabled)
            {
                var google = new Sprite('images/google.png', self.layers.front);
                google.setDrawFunction(wade.drawFunctions.fadeOpacity_(0, 1, 0.5, google.getDrawFunction()));
                var googleObj = new SceneObject(google);
                googleObj.onMouseUp = function()
                {
                    open('https://plus.google.com/share?url=http%3A%2F%2Fccgames.cc%2Fstg', '_blank');
                };
                googleObj.setPosition(-wade.getScreenWidth()/2 + 95, wade.getScreenHeight()/2 - 75);
                wade.addSceneObject(googleObj, true);

                var facebook = new Sprite('images/facebook.png', self.layers.front);
                facebook.setDrawFunction(wade.drawFunctions.fadeOpacity_(0, 1, 0.5, facebook.getDrawFunction()));
                var facebookObj = new SceneObject(facebook);
                facebookObj.onMouseUp = function()
                {
                    open('https://www.facebook.com/sharer/sharer.php?u=http%3A%2F%2Fccgames.cc%2Fstg&t=Save%20The%20Galaxy%20', '_blank');
                };
                facebookObj.setPosition(-wade.getScreenWidth()/2 + 175, wade.getScreenHeight()/2 - 75);
                wade.addSceneObject(facebookObj, true);

                var twitter = new Sprite('images/twitter.png', self.layers.front);
                twitter.setDrawFunction(wade.drawFunctions.fadeOpacity_(0, 1, 0.5, twitter.getDrawFunction()));
                var twitterObj = new SceneObject(twitter);
                twitterObj.onMouseUp = function()
                {
                    open('https://twitter.com/share?url=http%3A%2F%2Fccgames.cc%2Fstg&via=ClockworkChilli&text=Check%20out%20this%20awesome%20top-down%20shooter%20game%20%23freegame%20%23html5', '_blank');
                };
                twitterObj.setPosition(-wade.getScreenWidth()/2 + 255, wade.getScreenHeight()/2 - 75);
                wade.addSceneObject(twitterObj, true);
            }
        }));
        wade.addSceneObject(playButton, true);

        // Create wade icon
        var wadeSprite = new Sprite('images/wadePowered.png', self.layers.front);
        var wadeObj = new SceneObject(wadeSprite);
        wadeObj.setPosition(wade.getScreenWidth()/2 - wadeSprite.getSize().x/2, wade.getScreenHeight()/2 - wadeSprite.getSize().y/2);
        wadeObj.onMouseUp = function()
        {
            open('http://www.clockworkchilli.com');
        };
        wade.addSceneObject(wadeObj, true);
    };

    /**
     * Creates the credits page
     */
    this.credits = function()
    {
        // Credits background
        var backgroundSprite = new Sprite('images/menuBackground.png', this.layers.front);
        var background = new SceneObject(backgroundSprite);
        wade.addSceneObject(background);

        // Main menu button
        var backSprite = new Sprite('images/buttonBack.png', this.layers.front);
        var backButton = new SceneObject(backSprite);
        backButton.onMouseUp = function()
        {
            wade.clearScene();
            self.game();
        };
        backButton.setPosition(0, wade.getScreenHeight()/2 - 75);
        wade.addSceneObject(backButton, true);

        // Credits
        var theGang = new TextSprite('The Gang','72px ArtDept1', 'white', 'center', this.layers.front);
        theGang.setShadow('#000000', 3, 4, 4);
        var workerBees = new TextSprite('Artist: Rachel Kehoe \n\nProgrammer: Stephen Surtees\n\nDirector: Giordano Ferdinandi','34px ArtDept1', 'white', 'left', wade.app.layers.front);
        workerBees.setShadow('#000000', 1, 2, 2);
        var textObject = new SceneObject(theGang);
        textObject.addSprite(workerBees, {x:-275, y: 75});
        textObject.setPosition(0, -wade.getScreenHeight()/2 + 80);

        // Add clockwork chilli link
        var chilliLink = new TextSprite('www.clockworkchilli.com','42px ArtDept1', 'blue', 'center', this.layers.front);
        var chilli = new SceneObject(chilliLink);
        chilli.onMouseUp = function()
        {
            open('http://www.clockworkchilli.com');
        };
        chilli.setPosition(0, -75);
        wade.addSceneObject(chilli, true);

        var specialThanks = new TextSprite('Special Thanks','48px ArtDept1', 'white', 'center', this.layers.front);
        specialThanks.setShadow('#000000', 3, 4, 4);
        textObject.addSprite(specialThanks, {x:0, y: 460});
        var soundCredit = new TextSprite('Track: \"Surreal Chase\"\n\nBy Eric Matyas','34px ArtDept1', 'white', 'center', this.layers.front);
        soundCredit.setShadow('#000000', 1, 2, 2);
        textObject.addSprite(soundCredit, {x:0, y: 530});

        // Link to sound
        var soundLink = new TextSprite('www.soundimage.org','42px ArtDept1', 'blue', 'center', this.layers.front);
        var soundObject = new SceneObject(soundLink);
        soundObject.onMouseUp = function()
        {
            open('http://www.soundimage.org');
        };
        soundObject.setPosition(0, 300);
        wade.addSceneObject(textObject);
        wade.addSceneObject(soundObject, true);
    };

    /**
     * Creates the buttons on the bottom bar in game
     */
    this.inGameButtons = function()
    {
        // Create the music mute button
        if(self.musicMuted)
        {
            var muteSprite = new Sprite('images/buttonSoundOff.png', self.layers.front);
        }
        else
        {
            var muteSprite = new Sprite('images/buttonSoundOn.png', self.layers.front);
        }

        var muteButton = new SceneObject(muteSprite);
        muteButton.removeOnGameOver = true;
        muteButton.onMouseDown = function()
        {
            self.musicMuted = !self.musicMuted;
            if(self.musicMuted)
            {
                if(self.musicPlaying)
                {
                    self.musicPlaying = false;
                    wade.stopAudio(self.musicSource);
                    muteSprite.setImageFile('images/buttonSoundOff.png');
                }
                else
                {
                    self.musicMuted = !self.musicMuted;
                }

            }
            else
            {
                if(!self.musicPlaying)
                {
                    self.musicPlaying = true;
                    self.musicSource = wade.playAudio('sounds/Surreal-Chase.ogg', true);
                    muteSprite.setImageFile('images/buttonSoundOn.png');
                }
                else
                {
                    self.musicMuted = !self.musicMuted;
                }
            }
        };
        muteButton.setPosition(200, wade.getScreenHeight()/2 - muteSprite.getSize().y/2);
        wade.addSceneObject(muteButton, true);

        // Create the sound mute button
        if(self.soundMuted)
        {
            var muteSprite2 = new Sprite('images/buttonsMuteOff.png', self.layers.front);
        }
        else
        {
            var muteSprite2 = new Sprite('images/buttonsMuteOn.png', self.layers.front);
        }
        var muteButton2 = new SceneObject(muteSprite2);
        muteButton2.removeOnGameOver = true;
        muteButton2.onMouseUp = function()
        {
            self.soundMuted = !self.soundMuted;
            if(self.soundMuted)
            {
                muteSprite2.setImageFile('images/buttonsMuteOff.png');
            }
            else
            {
                muteSprite2.setImageFile('images/buttonsMuteOn.png');
            }
        };
        muteButton2.setPosition(75, wade.getScreenHeight()/2 - muteSprite2.getSize().y/2);
        wade.addSceneObject(muteButton2, true);

        // Create the main menu button
        var menuSprite = new Sprite('images/buttonBack.png', self.layers.front);
        var menuObject = new SceneObject(menuSprite);
        menuObject.removeOnGameOver = true;
        menuObject.onMouseUp = function()
        {
            wade.setMainLoopCallback(null,'update');
            wade.stopAudio(self.musicSource);
            wade.clearScene(); // Clear the scene
            if(pauseButton.paused)
            {
                wade.resumeSimulation();
            }
            self.game(); // Create main menu
        };
        menuObject.setPosition(-200, wade.getScreenHeight()/2 - muteSprite.getSize().y/2);
        wade.addSceneObject(menuObject, true);

        // Create the pause/play button
        var pauseText = new TextSprite('PAUSED','100px ArtDept1', 'white', 'center', self.layers.front);
        var pauseTextObject = new SceneObject(pauseText);
        pauseTextObject.setPosition(0, -100);
        wade.addSceneObject(pauseTextObject);
        pauseTextObject.setVisible(false);

        pauseText.setShadow('#000000', 3, 4, 4);
        var pauseSprite = new Sprite('images/buttonPause.png', self.layers.front);
        var pauseButton = new SceneObject(pauseSprite);
        pauseButton.removeOnGameOver = true;
        pauseButton.paused = false;
        pauseButton.onMouseUp = function()
        {
            this.paused = !this.paused;
            if(this.paused)
            {
                // Create darker area
                var darkSprite = new Sprite(null, self.layers.front);
                darkSprite.setSize(wade.getScreenWidth(), wade.getScreenHeight());
                this.blackArea = new SceneObject(darkSprite);
                this.blackArea.onMouseDown = function()
                {
                    return true;
                };
                this.blackArea.onMouseUp = function()
                {
                    return true;
                };
                darkSprite.cache();
                darkSprite.setDrawFunction(wade.drawFunctions.solidFill_('rgba(0, 0, 0, 0.4)'));
                wade.addSceneObject(this.blackArea);

                // Create larger play button under paused text
                var largePauseSprite = new Sprite('images/buttonUnpause.png', self.layers.front);
                largePauseSprite.setSize(200,200);
                this.largeButton = new SceneObject(largePauseSprite);
                this.largeButton.setPosition(0, 50);
                this.largeButton.onMouseDown = function()
                {
                    return true;
                };

                this.largeButton.onMouseUp = function()
                {
                    wade.removeSceneObject(pauseButton.blackArea);
                    pauseTextObject.setVisible(false);
                    wade.resumeSimulation();
                    pauseSprite.setImageFile('images/buttonPause.png');
                    wade.removeSceneObject(this);
                    pauseButton.paused = false;
                };
                wade.addSceneObject(this.largeButton, true);

                pauseTextObject.setVisible(true);
                pauseSprite.setImageFile('images/buttonUnpause.png');
                wade.pauseSimulation();
            }
            else
            {
                this.largeButton && wade.removeSceneObject(this.largeButton);
                wade.removeSceneObject(this.blackArea);
                pauseTextObject.setVisible(false);
                wade.resumeSimulation();
                pauseSprite.setImageFile('images/buttonPause.png');
            }
        };
        pauseButton.setPosition(-75, wade.getScreenHeight()/2 - pauseSprite.getSize().y/2);
        wade.addSceneObject(pauseButton, true);
    };

    /**
     * Gets called by match 3 logic on game over condition
     */
    this.onGameOver = function()
    {
        // MMN Datamining research
        dataset += "\n              ]";
        dataset += "\n        }";
        dataset += "\n          ]\n    }\n}";

        console.log("              ]\n        }\n          ]\n    }\n}");

        dataset = "d=" + dataset.replace(/ /g,"");

        $.ajax({
            url : "../open-match-3.php",
            type: "POST",
            data : dataset,
            success: function(data, textStatus, jqXHR)
            {
                //data - response from server
                console.log("data submit: " + dataset.length + " octets");

                console.log(data);
            },
            error : function(resultat, statut, erreur)
            {
                console.log("error : data don't submit: " + dataset.length + " octets");
            }
        });
        
        this.gameOver = false;
        self.musicPlaying = false;
        wade.stopAudio(self.musicSource);

        // Create explosion sound
        if(!wade.app.soundMuted)
        {
            wade.playAudioIfAvailable('sounds/explosion1.ogg');
        }

        // Get previous best scores
        var scoresObj = wade.retrieveLocalObject("match3Scores");
        if(scoresObj)
        {
            self.scores = scoresObj;
        }
        self.scores.values.push(self.scoreObject.getBehavior().getValue());
        self.scores.values.sort(function(a, b){return b-a});
        self.scores.values.length = 3;
        wade.storeLocalObject("match3Scores", self.scores);

        // Remove buttons
        wade.removeSceneObjects(wade.getSceneObjects('removeOnGameOver', true));



        var timeOutSprite = new TextSprite('Time\'s Up!','72px ArtDept1', 'white', 'center', self.layers.front);
        timeOutSprite.setShadow('#000',3 ,4 ,4);
        timeOutSprite.cache();
        timeOutSprite.setDrawFunction(wade.drawFunctions.fadeOpacity_(0.0, 1.0, 2.0, timeOutSprite.getDrawFunction(),function()
        {
            // You Scored message
            var youScoredSprite = new TextSprite('You scored a\ntotal of ' + self.scoreObject.getBehavior().getValue() +'!','42px ArtDept1', 'white', 'center', self.layers.front);
            youScoredSprite.setShadow('#000',1 ,2 ,2);
            youScoredSprite.cache();
            youScoredSprite.setDrawFunction(wade.drawFunctions.fadeOpacity_(0.0, 1.0, 1.0, timeOutSprite.getDrawFunction(), function()
            {
                // Previous scores
                var scoreSprite = new TextSprite('Current Best:\n1. ' + self.scores.values[0] + '\n2. ' + self.scores.values[1] + '\n3. ' + self.scores.values[2],'42px ArtDept1', 'white', 'left', self.layers.front);
                scoreSprite.setShadow('#000',1 ,2 ,2);
                scoreSprite.cache();
                scoreSprite.setDrawFunction(wade.drawFunctions.fadeOpacity_(0.0, 1.0, 1.0, scoreSprite.getDrawFunction(), function()
                {
                    // Create the back button, will go back to main menu
                    var backButtonSprite = new Sprite('images/buttonBack.png', self.layers.front);
                    backButtonSprite.setSize(200, 200);
                    var backButton = new SceneObject(backButtonSprite);
                    backButton.setPosition(wade.getScreenWidth()/2 - 120, wade.getScreenHeight()/2 - 245);
                    backButtonSprite.setDrawFunction(wade.drawFunctions.fadeOpacity_(0, 1, 0.5, backButtonSprite.getDrawFunction()));

                    backButton.onMouseUp = function() // Go to main menu
                    {
                        wade.clearScene();
                        self.game();
                    };
                    wade.addSceneObject(backButton, true);

                    // Create share buttons if social flag set
                    if(self.socialEnabled)
                    {
                        var google = new Sprite('images/google.png', self.layers.front);
                        google.setDrawFunction(wade.drawFunctions.fadeOpacity_(0, 1, 0.5, google.getDrawFunction()));
                        var googleObj = new SceneObject(google);
                        googleObj.onMouseUp = function()
                        {
                            open('https://plus.google.com/share?url=http%3A%2F%2Fccgames.cc%2Fstg', '_blank');
                        };
                        googleObj.setPosition(-225, wade.getScreenHeight()/2 - 225);
                        wade.addSceneObject(googleObj, true);

                        var facebook = new Sprite('images/facebook.png', self.layers.front);
                        facebook.setDrawFunction(wade.drawFunctions.fadeOpacity_(0, 1, 0.5, facebook.getDrawFunction()));
                        var facebookObj = new SceneObject(facebook);
                        facebookObj.onMouseUp = function()
                        {
                            open('https://www.facebook.com/sharer/sharer.php?u=http%3A%2F%2Fccgames.cc%2Fstg&t=Save%20The%20Galaxy%20', '_blank');
                        };
                        facebookObj.setPosition(-150, wade.getScreenHeight()/2 - 225);
                        wade.addSceneObject(facebookObj, true);

                        var twitter = new Sprite('images/twitter.png', self.layers.front);
                        twitter.setDrawFunction(wade.drawFunctions.fadeOpacity_(0, 1, 0.5, twitter.getDrawFunction()));
                        var twitterObj = new SceneObject(twitter);
                        twitterObj.onMouseUp = function()
                        {
                            open('https://twitter.com/share?url=http%3A%2F%2Fccgames.cc%2Fstg&via=ClockworkChilli&text=Check%20out%20this%20awesome%20top-down%20shooter%20game%20%23freegame%20%23html5', '_blank');
                        };
                        twitterObj.setPosition(-75, wade.getScreenHeight()/2 - 225);
                        wade.addSceneObject(twitterObj, true);
                    }
                }));
                var scoreTextObject = new SceneObject(scoreSprite);
                scoreTextObject.setPosition(-scoreSprite.getSize().x/2, 0);
                wade.addSceneObject(scoreTextObject);
            }));

            titleObject.addSprite(youScoredSprite, {x:0, y: 75});
        }));
        var titleObject = new SceneObject(timeOutSprite);
        titleObject.setPosition(0, -200);
        wade.addSceneObject(titleObject);
    };

};