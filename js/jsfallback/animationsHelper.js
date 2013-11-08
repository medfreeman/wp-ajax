// Sample library written by David Rousset from Microsoft France - http://blogs.msdn.com/davrous
// Built to add a JS fallback to specific CSS3 animations for IE9  
// Based on the concept of TRANSITIONSHELPER built by David Catuhe - http://blogs.msdn.com/eternalcoding
var ANIMATIONSHELPER = ANIMATIONSHELPER || {};

ANIMATIONSHELPER.tickIntervalID = 0;

ANIMATIONSHELPER.easingFunctions = {
    linear:0,
    ease:1,
    easein:2,
    easeout:3,
    easeinout:4,
    custom:5
};

// array containing all the current transitions played
ANIMATIONSHELPER.currentTransitions = [];

ANIMATIONSHELPER.computeCubicBezierCurveInterpolation = function (t, x1, y1, x2, y2) {
    // Extract X (which is equal to time here)
    var f0 = 1 - 3 * x2 + 3 * x1;
    var f1 = 3 * x2 - 6 * x1;
    var f2 = 3 * x1;

    var refinedT = t;
    for (var i = 0; i < 5; i++) {
        var refinedT2 = refinedT * refinedT;
        var refinedT3 = refinedT2 * refinedT;

        var x = f0 * refinedT3 + f1 * refinedT2 + f2 * refinedT;
        var slope = 1.0 / (3.0 * f0 * refinedT2 + 2.0 * f1 * refinedT + f2);
        refinedT -= (x - t) * slope;
        refinedT = Math.min(1, Math.max(0, refinedT));
    }

    // Resolve cubic bezier for the given x
    return 3 * Math.pow(1 - refinedT, 2) * refinedT * y1 +
            3 * (1 - refinedT) * Math.pow(refinedT, 2) * y2 +
            Math.pow(refinedT, 3);
};

ANIMATIONSHELPER.extractValues = function (string, name) {
    var valueIndex = 0;
    var valueArray = string.split(" ");
    var originalValues = [];
    try {
        //for (var i = 0; i < valueArray.length; i++) {
        //currentString = valueArray[i];
            currentString = string;
            if (currentString.indexOf("rotate") !== -1) {
                var subString = currentString.substring(currentString.indexOf("(") + 1, currentString.indexOf("deg)"));
                originalValues.push({ deg: parseFloat(subString) });
            }

            if (currentString.indexOf("translate") !== -1) {
                var subString1 = currentString.substring(currentString.indexOf("(") + 1, currentString.indexOf("px,"));
                var subString2 = currentString.substring(currentString.indexOf(",") + 1, currentString.indexOf("px)"));
                originalValues.push({ x: parseFloat(subString1), y: parseFloat(subString2) });
            }
        //}
        
        return originalValues;
    } catch (e) {
        return 0;
    }
};

// Helper to get the appropriate vendor's prefix property name
ANIMATIONSHELPER.getTransformProperty = function () {
    var element = document.createElement("div");

    var properties = [
        'transform',
        'WebkitTransform',
        'msTransform',
        'MozTransform',
        'OTransform'
    ];
    var p;
    while (p = properties.shift()) {
        if (typeof element.style[p] != 'undefined') {
            return p;
        }
    }
    return false;
};

// Saving the current vendor prefix
ANIMATIONSHELPER.currentTransformProperty = ANIMATIONSHELPER.getTransformProperty();

// This method is a very specific code to work only with animations/keyframes done on 
// the rotation & translation CSS3 2d transformations
ANIMATIONSHELPER.tick = function () {
    // Processing transitions
    for (var index = 0; index < ANIMATIONSHELPER.currentTransitions.length; index++) {
        var transition = ANIMATIONSHELPER.currentTransitions[index];

        // compute new value
        var currentDate = (new Date).getTime();
        var diff = currentDate - transition.startDate;
        var step;

        if (transition.duration !== 0) {
            step = diff / transition.duration;
        }
        else {
            step = 0;
        }

        var offset = 1;
        var offsetRotation = 1;
        var offsetTranslationX = 1;
        var offsetTranslationY = 1;
        var originalRotationValue = 0;
        var currentRotationValue = -1;
        var originalTranslationXValue = 0;
        var currentTranslationXValue = -1;
        var originalTranslationYValue = 0;
        var currentTranslationYValue = -1;
        var newTransformValues = "";

        // Timing function
        switch (transition.ease) {
            case ANIMATIONSHELPER.easingFunctions.linear:
                offset = ANIMATIONSHELPER.computeCubicBezierCurveInterpolation(step, 0, 0, 1.0, 1.0);
                break;
            case ANIMATIONSHELPER.easingFunctions.ease:
                offset = ANIMATIONSHELPER.computeCubicBezierCurveInterpolation(step, 0.25, 0.1, 0.25, 1.0);
                break;
            case ANIMATIONSHELPER.easingFunctions.easein:
                offset = ANIMATIONSHELPER.computeCubicBezierCurveInterpolation(step, 0.42, 0, 1.0, 1.0);
                break;
            case ANIMATIONSHELPER.easingFunctions.easeout:
                offset = ANIMATIONSHELPER.computeCubicBezierCurveInterpolation(step, 0, 0, 0.58, 1.0);
                break;
            case ANIMATIONSHELPER.easingFunctions.easeinout:
                offset = ANIMATIONSHELPER.computeCubicBezierCurveInterpolation(step, 0.42, 0, 0.58, 1.0);
                break;
            case ANIMATIONSHELPER.easingFunctions.custom:
                offset = ANIMATIONSHELPER.computeCubicBezierCurveInterpolation(step, transition.customEaseP1X, transition.customEaseP1Y, transition.customEaseP2X, transition.customEaseP2Y);
                break;
        }

        if (transition.properties[0] == "rotate") {
            if (transition.originalValues[0] && transition.originalValues[0].deg) {
                originalRotationValue = transition.originalValues[0].deg;
            }

            offsetRotation = offset * (transition.finalValues[0].deg - originalRotationValue);
            currentRotationValue = originalRotationValue + offsetRotation;
        }

        if (transition.properties[0] == "translate") {
            if (transition.originalValues[0] && transition.originalValues[0].x) {
                originalTranslationXValue = transition.originalValues[0].x;
                originalTranslationYValue = transition.originalValues[0].y;
            }

            offsetTranslationX = offset * (transition.finalValues[0].x - originalTranslationXValue);
            offsetTranslationY = offset * (transition.finalValues[0].y - originalTranslationYValue);
            currentTranslationXValue = originalTranslationXValue + offsetTranslationX;
            currentTranslationYValue = originalTranslationYValue + offsetTranslationY;
        }

        transition.currentDate = currentDate;

        // Dead transition?
        if (currentDate >= transition.startDate + transition.duration) {
            if (currentRotationValue !== -1) {
                currentRotationValue = transition.finalValues[0].deg;
            }
            if (currentTranslationXValue !== -1) {
                currentTranslationXValue = transition.finalValues[0].x;
            }
            if (currentTranslationYValue !== -1) {
                currentTranslationYValue = transition.finalValues[0].y;
            }

            transition.isPlaying = false;
            ANIMATIONSHELPER.currentTransitions.splice(index, 1); // Removing transition
            index--;
        }

        // Affect it
        if (currentRotationValue !== -1) {
            newTransformValues = "rotate(" + currentRotationValue + "deg)";
        }

        if (currentTranslationXValue !== -1) {
            newTransformValues = "translate(" + currentTranslationXValue + "px," + currentTranslationYValue + "px)";
        }

        transition.target.style[ANIMATIONSHELPER.currentTransformProperty] = newTransformValues;
    }
};

// Animation object
// It need the HTML targeted element, the name of the animation, its duration & iteration count and
// the keyframes contained in an array object
// View the animation simply as a sequence of transitions played a certain number of times
ANIMATIONSHELPER.animation = function (target, name, duration, iterationcount, keyframes) {
    // saving the properties values
    this.name = name;
    this.duration = duration;
    this.iterationcount = iterationcount;
    this.target = target;

    var elapsedtime = 0;
    var keyframeduration = 0;
    var elapsedtime = 0;

    // Transforming the percentage of each keyframe into duration value
    for (var i = 0; i < keyframes.length; i++) {
        keyframeduration = ((keyframes[i].percentage * duration) / 100) - elapsedtime;
        keyframes[i].duration = keyframeduration;
        elapsedtime += keyframeduration;
    }

    this.currentTransition = { isPlaying: false };
    this.keyframes = keyframes;
    this.keyframesCount = keyframes.length;
    this.currentKeyFrameIndex = 0;

    // The nextTransition() function return the next transition to run
    // based on the current keyframe to play
    this.nextTransition = function (keyframe, ease, customEaseP1X, customEaseP1Y, customEaseP2X, customEaseP2Y) {
        var properties = [];
        var finalValues = [];
        var transition;

        // Compared to the original TRANSITIONSHELPER of David Catuhe
        // We need a specific code to play with the CSS3 2D Transform properties values
        if (keyframe.propertyToAnimate === "transform") {
            for (var i = 0; i < keyframe.transformType.length; i++) {
                properties.push(keyframe.transformType[i].type);
                if (keyframe.transformType[i].type == "rotate") {
                    finalValues.push({ deg: keyframe.transformType[i].value1 });
                }
                else {
                    finalValues.push({ x: keyframe.transformType[i].value1, y: keyframe.transformType[i].value2 });
                }
            }

            // Create a new transition
            transition = {
                name: this.name + this.currentKeyFrameIndex,
                target: this.target,
                properties: properties,
                finalValues: finalValues,
                originalValues: ANIMATIONSHELPER.extractValues(target.style[ANIMATIONSHELPER.currentTransformProperty], this.name),
                duration: keyframe.duration,
                startDate: (new Date).getTime(),
                currentDate: (new Date).getTime(),
                ease: ease,
                customEaseP1X: customEaseP1X,
                customEaseP2X: customEaseP2X,
                customEaseP1Y: customEaseP1Y,
                customEaseP2Y: customEaseP2Y,
                isPlaying: true,
                type: "transform"
            };

            return transition;
        }
        // If it's a classic property to animate, we're using more or less the TRANSITIONSHELPER as-is
        else {
            return TRANSITIONSHELPER.transition(this.target, keyframe.propertyToAnimate, keyframe.value, keyframe.duration, TRANSITIONSHELPER.easingFunctions.linear);
        }
    };

    // each animation object has a tick function
    // that will be called every 17 ms (to target 60 fps)
    // This ticker is monitoring the current state of the transition and
    // create a new transition as soon as the old one is finished/dead
    this.tick = function () {
        if (this.iterationcount > 0) {
            if (!this.currentTransition.isPlaying) {
                this.currentTransition = this.nextTransition(this.keyframes[this.currentKeyFrameIndex], ANIMATIONSHELPER.easingFunctions.linear);
                // We're using our own global ticker only for the 2D transformations
                // Otherwise, we're using the one from the TRANSITIONSHELPER library
                if (this.currentTransition.type === "transform") {
                    ANIMATIONSHELPER.currentTransitions.push(this.currentTransition);
                }
                this.currentKeyFrameIndex++;

                // We've reached the last keyframe (100%). We're starting back from the beginning
                if (this.currentKeyFrameIndex >= this.keyframesCount) {
                    this.currentKeyFrameIndex = 0;
                    this.iterationcount--;
                }
            }
        }
    };
};

// Object to build a new generic keyframe (not working on the CSS3 2D Transform properties thus)
ANIMATIONSHELPER.keyframe = function (percentage, propertyToAnimate, value) {
    this.percentage = percentage;
    this.propertyToAnimate = propertyToAnimate;
    this.value = value;
};

//Objects to build a specific rotation & translation keyframes
ANIMATIONSHELPER.rotationkeyframe = function (percentage, value) {
    this.percentage = percentage;
    this.propertyToAnimate = "transform";
    this.transformType = [];
    this.transformType.push(new ANIMATIONSHELPER.transformType("rotate", value));
};

ANIMATIONSHELPER.translationkeyframe = function (percentage, valuex, valuey) {
    this.percentage = percentage;
    this.propertyToAnimate = "transform";
    this.transformType = [];
    this.transformType.push(new ANIMATIONSHELPER.transformType("translate", valuex, valuey));
};

// 2 kind of transformations supported: rotation & translation
ANIMATIONSHELPER.transformType = function (type, value1, value2) {
    this.type = type;
    // rotation in deg or translation X in px
    this.value1 = value1;
    // translation Y in px
    this.value2 = value2;
}

ANIMATIONSHELPER.launchAnimation = function (animation) {
    // Launching the tick service if required
    if (ANIMATIONSHELPER.tickIntervalID == 0) {
        ANIMATIONSHELPER.tickIntervalID = setInterval(ANIMATIONSHELPER.tick, 17);
    }

    // Little closure to launch the tick method on the appropriate animation instance
    setInterval(function () { animation.tick(); }, 17);
};

ANIMATIONSHELPER.stop = function () {
    if (ANIMATIONSHELPER.tickIntervalID != 0) {
        clearInterval()(ANIMATIONSHELPER.tick);
        ANIMATIONSHELPER.tickIntervalID = 0;
    }
}