"use strict";
class Logger {
    constructor(level) {
        this.level = level;
        this.warnCounter = 0;
    }
    log(s) {
        if (this.level == "none")
            return;
        if (this.level == "warn") {
            if (this.warnCounter > 0)
                console.log(s);
        }
        else {
            if (this.warnCounter > 0)
                console.log(s);
            else
                console.log(s);
        }
    }
    beginWarn() {
        this.warnCounter++;
    }
    endWarn() {
        this.warnCounter--;
    }
}
exports.Logger = Logger;
//# sourceMappingURL=Logger.js.map