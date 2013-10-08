// Using the ANIMATIONSHELPER sample library written by David Rousset from Microsoft France - http://blogs.msdn.com/davrous
// Rewritting the keyframes & anizmations extracted from master.css

function LoadJSAnimationsFallback() {
    // number of times you'd like the animations to be run
    var iterationsNumber = 100;

    var skullElement = document.getElementById("skull");
    var keyframes = [];
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(25, 15));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(50, -5));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(55, 0));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(75, -10));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(100, 0));

    var animation1 = new ANIMATIONSHELPER.animation(skullElement, "rotate-skull", 7000, iterationsNumber, keyframes);

    shellElement = document.getElementById("shell");
    keyframes = [];
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(20, 3));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(40, 0));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(50, 0));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(70, -3));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(90, 0));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(100, 0));

    var animation2 = new ANIMATIONSHELPER.animation(shellElement, "rotate-shell", 4000, iterationsNumber, keyframes);

    var legaElement = document.getElementById("leg-a");
    keyframes = [];
    keyframes.push(new ANIMATIONSHELPER.translationkeyframe(75, 0, 0));
    keyframes.push(new ANIMATIONSHELPER.translationkeyframe(80, 5, 0));
    keyframes.push(new ANIMATIONSHELPER.translationkeyframe(85, 5, 5));
    keyframes.push(new ANIMATIONSHELPER.translationkeyframe(90, -5, 5));
    keyframes.push(new ANIMATIONSHELPER.translationkeyframe(95, -5, 0));
    keyframes.push(new ANIMATIONSHELPER.translationkeyframe(100, 0, 0));

    var animation3 = new ANIMATIONSHELPER.animation(legaElement, "rotate-hip-a", 7000, iterationsNumber, keyframes);

    var legbElement = document.getElementById("leg-b");
    keyframes = [];
    keyframes.push(new ANIMATIONSHELPER.translationkeyframe(55, 0, 0));
    keyframes.push(new ANIMATIONSHELPER.translationkeyframe(60, 5, 0));
    keyframes.push(new ANIMATIONSHELPER.translationkeyframe(65, 5, 5));
    keyframes.push(new ANIMATIONSHELPER.translationkeyframe(70, -5, 5));
    keyframes.push(new ANIMATIONSHELPER.translationkeyframe(75, -5, 0));
    keyframes.push(new ANIMATIONSHELPER.translationkeyframe(80, 0, 0));
    keyframes.push(new ANIMATIONSHELPER.translationkeyframe(100, 0, 0));

    var animation4 = new ANIMATIONSHELPER.animation(legbElement, "rotate-hip-b", 8000, iterationsNumber, keyframes);

    var legcElement = document.getElementById("leg-c");
    keyframes = [];
    keyframes.push(new ANIMATIONSHELPER.translationkeyframe(25, 0, 0));
    keyframes.push(new ANIMATIONSHELPER.translationkeyframe(30, 5, 0));
    keyframes.push(new ANIMATIONSHELPER.translationkeyframe(35, 5, 5));
    keyframes.push(new ANIMATIONSHELPER.translationkeyframe(40, -5, 5));
    keyframes.push(new ANIMATIONSHELPER.translationkeyframe(45, -5, 0));
    keyframes.push(new ANIMATIONSHELPER.translationkeyframe(50, 0, 0));
    keyframes.push(new ANIMATIONSHELPER.translationkeyframe(100, 0, 0));

    var animation5 = new ANIMATIONSHELPER.animation(legcElement, "rotate-hip-c", 7000, iterationsNumber, keyframes);

    var legdElement = document.getElementById("leg-d");
    keyframes = [];
    keyframes.push(new ANIMATIONSHELPER.translationkeyframe(5, 5, 0));
    keyframes.push(new ANIMATIONSHELPER.translationkeyframe(10, 5, 5));
    keyframes.push(new ANIMATIONSHELPER.translationkeyframe(15, -5, 5));
    keyframes.push(new ANIMATIONSHELPER.translationkeyframe(20, -5, 0));
    keyframes.push(new ANIMATIONSHELPER.translationkeyframe(25, 0, 0));
    keyframes.push(new ANIMATIONSHELPER.translationkeyframe(100, 0, 0));

    var animation6 = new ANIMATIONSHELPER.animation(legdElement, "rotate-hip-d", 7000, iterationsNumber, keyframes);

    var legathighElement = document.getElementById("leg-a").getElementsByClassName("leg-thigh")[0];
    keyframes = [];
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(70, 0));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(80, -13));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(100, 0));

    var animation7 = new ANIMATIONSHELPER.animation(legathighElement, "thigh-a", 7000, iterationsNumber, keyframes);

    var legbthighElement = document.getElementById("leg-b").getElementsByClassName("leg-thigh")[0];
    keyframes = [];
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(50, 0));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(60, -13));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(80, 0));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(100, 0));

    var animation8 = new ANIMATIONSHELPER.animation(legbthighElement, "thigh-b", 7000, iterationsNumber, keyframes);

    var legcthighElement = document.getElementById("leg-c").getElementsByClassName("leg-thigh")[0];
    keyframes = [];
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(20, 0));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(30, -13));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(50, 0));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(100, 0));

    var animation9 = new ANIMATIONSHELPER.animation(legcthighElement, "thigh-c", 7000, iterationsNumber, keyframes);

    var legdthighElement = document.getElementById("leg-d").getElementsByClassName("leg-thigh")[0];
    keyframes = [];
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(10, -13));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(30, -0));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(100, 0));

    var animation10 = new ANIMATIONSHELPER.animation(legdthighElement, "thigh-d", 7000, iterationsNumber, keyframes);

    var legashinElement = document.getElementById("leg-a").getElementsByClassName("leg-shin")[0];
    keyframes = [];
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(0, 0));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(70, 0));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(80, 23));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(100, 0));

    var animation11 = new ANIMATIONSHELPER.animation(legashinElement, "shin-a", 7000, iterationsNumber, keyframes);

    var legbshinElement = document.getElementById("leg-b").getElementsByClassName("leg-shin")[0];
    keyframes = [];
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(50, 0));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(60, 23));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(80, 0));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(100, 0));

    var animation12 = new ANIMATIONSHELPER.animation(legbshinElement, "shin-b", 7000, iterationsNumber, keyframes);

    var legcshinElement = document.getElementById("leg-c").getElementsByClassName("leg-shin")[0];
    keyframes = [];
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(20, 0));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(30, 23));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(50, 0));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(100, 0));

    var animation13 = new ANIMATIONSHELPER.animation(legcshinElement, "shin-c", 7000, iterationsNumber, keyframes);

    var legdshinElement = document.getElementById("leg-d").getElementsByClassName("leg-shin")[0];
    keyframes = [];
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(10, 23));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(30, 0));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(100, 0));

    var animation14 = new ANIMATIONSHELPER.animation(legdshinElement, "shin-d", 7000, iterationsNumber, keyframes);

    var legafootElement = document.getElementById("leg-a").getElementsByClassName("leg-foot")[0];
    keyframes = [];
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(70, 0));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(80, -20));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(100, 0));

    var animation15 = new ANIMATIONSHELPER.animation(legafootElement, "foot-a", 7000, iterationsNumber, keyframes);

    var legbfootElement = document.getElementById("leg-b").getElementsByClassName("leg-foot")[0];
    keyframes = [];
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(50, 0));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(60, -20));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(80, 0));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(100, 0));

    var animation16 = new ANIMATIONSHELPER.animation(legbfootElement, "foot-b", 7000, iterationsNumber, keyframes);

    var legcfootElement = document.getElementById("leg-c").getElementsByClassName("leg-foot")[0];
    keyframes = [];
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(20, 0));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(30, -20));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(50, 0));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(100, 0));

    var animation17 = new ANIMATIONSHELPER.animation(legcfootElement, "foot-c", 7000, iterationsNumber, keyframes);

    var legdfootElement = document.getElementById("leg-d").getElementsByClassName("leg-foot")[0];
    keyframes = [];
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(10, -20));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(30, 0));
    keyframes.push(new ANIMATIONSHELPER.rotationkeyframe(100, 0));

    var animation18 = new ANIMATIONSHELPER.animation(legdfootElement, "foot-d", 7000, iterationsNumber, keyframes);

    var screenElement = document.getElementById("the-screen");
    keyframes = [];
    keyframes.push(new ANIMATIONSHELPER.keyframe(100, "background-position-x", -4203));
    keyframes.push(new ANIMATIONSHELPER.keyframe(0, "background-position-x", 0));

    var animation19 = new ANIMATIONSHELPER.animation(screenElement, "screen", 300000, iterationsNumber, keyframes);

    ANIMATIONSHELPER.launchAnimation(animation1, ANIMATIONSHELPER.easingFunctions.linear);
    ANIMATIONSHELPER.launchAnimation(animation2, ANIMATIONSHELPER.easingFunctions.linear);
    ANIMATIONSHELPER.launchAnimation(animation3, ANIMATIONSHELPER.easingFunctions.linear);
    ANIMATIONSHELPER.launchAnimation(animation4, ANIMATIONSHELPER.easingFunctions.linear);
    ANIMATIONSHELPER.launchAnimation(animation5, ANIMATIONSHELPER.easingFunctions.linear);
    ANIMATIONSHELPER.launchAnimation(animation6, ANIMATIONSHELPER.easingFunctions.linear);
    ANIMATIONSHELPER.launchAnimation(animation7, ANIMATIONSHELPER.easingFunctions.linear);
    ANIMATIONSHELPER.launchAnimation(animation8, ANIMATIONSHELPER.easingFunctions.linear);
    ANIMATIONSHELPER.launchAnimation(animation9, ANIMATIONSHELPER.easingFunctions.linear);
    ANIMATIONSHELPER.launchAnimation(animation10, ANIMATIONSHELPER.easingFunctions.linear);
    ANIMATIONSHELPER.launchAnimation(animation11, ANIMATIONSHELPER.easingFunctions.linear);
    ANIMATIONSHELPER.launchAnimation(animation12, ANIMATIONSHELPER.easingFunctions.linear);
    ANIMATIONSHELPER.launchAnimation(animation13, ANIMATIONSHELPER.easingFunctions.linear);
    ANIMATIONSHELPER.launchAnimation(animation14, ANIMATIONSHELPER.easingFunctions.linear);
    ANIMATIONSHELPER.launchAnimation(animation15, ANIMATIONSHELPER.easingFunctions.linear);
    ANIMATIONSHELPER.launchAnimation(animation16, ANIMATIONSHELPER.easingFunctions.linear);
    ANIMATIONSHELPER.launchAnimation(animation17, ANIMATIONSHELPER.easingFunctions.linear);
    ANIMATIONSHELPER.launchAnimation(animation18, ANIMATIONSHELPER.easingFunctions.linear);
    ANIMATIONSHELPER.launchAnimation(animation19, ANIMATIONSHELPER.easingFunctions.linear);

};