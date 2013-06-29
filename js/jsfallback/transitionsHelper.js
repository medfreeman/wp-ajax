var TRANSITIONSHELPER = TRANSITIONSHELPER || {};

TRANSITIONSHELPER.tickIntervalID = 0;

TRANSITIONSHELPER.easingFunctions = {
    linear:0,
    ease:1,
    easein:2,
    easeout:3,
    easeinout:4,
    custom:5
};

TRANSITIONSHELPER.currentTransitions = [];

TRANSITIONSHELPER.computeCubicBezierCurveInterpolation = function (t, x1, y1, x2, y2) {
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

TRANSITIONSHELPER.extractValue = function (string) {
    try {
        var result = parseFloat(string);

        if (isNaN(result)) {
            return 0;
        }

        return result;
    } catch (e) {
        return 0;
    }
};

TRANSITIONSHELPER.extractUnit = function (string) {

    // if value is empty we assume that it is px
    if (string == "") {
        return "px";
    }

    var value = TRANSITIONSHELPER.extractValue(string);
    var unit = string.replace(value, "");

    return unit;
};

TRANSITIONSHELPER.tick = function () {
    // Processing transitions
    for (var index = 0; index < TRANSITIONSHELPER.currentTransitions.length; index++) {
        var transition = TRANSITIONSHELPER.currentTransitions[index];

        // compute new value
        var currentDate = (new Date).getTime();
        var diff = currentDate - transition.startDate;

        var step = diff / transition.duration;
        var offset = 1;

        // Timing function
        switch (transition.ease) {
            case TRANSITIONSHELPER.easingFunctions.linear:
                offset = TRANSITIONSHELPER.computeCubicBezierCurveInterpolation(step, 0, 0, 1.0, 1.0);
                break;
            case TRANSITIONSHELPER.easingFunctions.ease:
                offset = TRANSITIONSHELPER.computeCubicBezierCurveInterpolation(step, 0.25, 0.1, 0.25, 1.0);
                break;
            case TRANSITIONSHELPER.easingFunctions.easein:
                offset = TRANSITIONSHELPER.computeCubicBezierCurveInterpolation(step, 0.42, 0, 1.0, 1.0);
                break;
            case TRANSITIONSHELPER.easingFunctions.easeout:
                offset = TRANSITIONSHELPER.computeCubicBezierCurveInterpolation(step, 0, 0, 0.58, 1.0);
                break;
            case TRANSITIONSHELPER.easingFunctions.easeinout:
                offset = TRANSITIONSHELPER.computeCubicBezierCurveInterpolation(step, 0.42, 0, 0.58, 1.0);
                break;
            case TRANSITIONSHELPER.easingFunctions.custom:
                offset = TRANSITIONSHELPER.computeCubicBezierCurveInterpolation(step, transition.customEaseP1X, transition.customEaseP1Y, transition.customEaseP2X, transition.customEaseP2Y);
                break;   
        }

        offset *= (transition.finalValue - transition.originalValue);

        var unit = TRANSITIONSHELPER.extractUnit(transition.target.style[transition.property]);
        var currentValue = transition.originalValue + offset;

        transition.currentDate = currentDate;

        // Dead transition?
        if (currentDate >= transition.startDate + transition.duration) {
            currentValue = transition.finalValue; // Clamping
            transition.isPlaying = false;
            TRANSITIONSHELPER.currentTransitions.splice(index, 1); // Removing transition
            index--;
        }

        // Affect it
        transition.target.style[transition.property] = currentValue + unit;
    }
};

TRANSITIONSHELPER.transition = function (target, property, newValue, duration, ease, customEaseP1X, customEaseP1Y, customEaseP2X, customEaseP2Y) {

    // Create a new transition
    var transition = {
        target: target,
        property: property,
        finalValue: newValue,
        originalValue: TRANSITIONSHELPER.extractValue(target.style[property]),
        duration: duration,
        startDate: (new Date).getTime(),
        currentDate: (new Date).getTime(),
        ease: ease,
        customEaseP1X: customEaseP1X,
        customEaseP2X: customEaseP2X,
        customEaseP1Y: customEaseP1Y,
        customEaseP2Y: customEaseP2Y,
        isPlaying: true
    };

    // Launching the tick service if required
    if (TRANSITIONSHELPER.tickIntervalID == 0) {
        TRANSITIONSHELPER.tickIntervalID = setInterval(TRANSITIONSHELPER.tick, 17);
    }

    // Remove previous transitions on same property and target
    for (var index = 0; index < TRANSITIONSHELPER.currentTransitions.length; index++) {
        var temp = TRANSITIONSHELPER.currentTransitions[index];

        if (temp.target === transition.target && temp.property === transition.property) {
            TRANSITIONSHELPER.currentTransitions.splice(index, 1);
            index--;
        }
    }

    // Register
    if (transition.originalValue != transition.finalValue) {
        TRANSITIONSHELPER.currentTransitions.push(transition);
    }

    return transition;
};

TRANSITIONSHELPER.stop = function () {
    if (TRANSITIONSHELPER.tickIntervalID != 0) {
        clearInterval()(TRANSITIONSHELPER.tick);
        TRANSITIONSHELPER.tickIntervalID = 0;
    }
}
