"use strict";
/**
 * Usage example:
 * var parser = new CmdOptions();
 * parser.addOption('isRecursive', false, [ '-r', '--recursive']);
 * parser.addOption('count', 0, [ '-c', '--count']);
 * parser.addOption('file', 'bin');
 * parser.parse([ 'test', '-c', '10', '-r' ]);
 * // now:
 * // parser.params = { 'c' => 10, 'r' => true, file => 'test' }
 */
class CmdOptions {
    constructor() {
        this.options = [];
    }
    get(name) {
        return this.params.get(name);
    }
    add(name, defaultValue, switches, help = "") {
        this.addInner(name, defaultValue, switches, help, false);
    }
    addRepeatable(name, switches, help = "") {
        this.addInner(name, [], switches, help, true);
    }
    addInner(name, defaultValue, switches, help, repeatable) {
        if (!this.hasOption(name)) {
            this.options.push({ name: name, defaultValue: defaultValue, switches: switches, help: help, repeatable: repeatable });
        }
        else {
            throw "Option '" + name + "' already added.";
        }
    }
    getHelpMessage(prefix = "\t") {
        var maxSwitchLength = 0;
        for (let opt of this.options) {
            if (opt.switches != null && opt.switches.length > 0) {
                maxSwitchLength = Math.max(maxSwitchLength, opt.switches.join(", ").length);
            }
            else {
                maxSwitchLength = Math.max(maxSwitchLength, opt.name.length + 2);
            }
        }
        var s = "";
        for (let opt of this.options) {
            if (opt.switches != null && opt.switches.length > 0) {
                s += prefix + this.rpad(opt.switches.join(", "), " ", maxSwitchLength + 1);
            }
            else {
                s += prefix + this.rpad("<" + opt.name + ">", " ", maxSwitchLength + 1);
            }
            if (opt.help != null && opt.help != "") {
                var helpLines = opt.help.split("\n");
                s += helpLines.shift() + "\n";
                s += helpLines.map(s => prefix + this.lpad("", " ", maxSwitchLength + 1) + s + "\n").join("");
            }
            else {
                s += "\n";
            }
            s += "\n";
        }
        while (s.endsWith("\n"))
            s = s.substring(0, s.length - 1);
        return s + "\n";
    }
    parse(args) {
        this.args = args.slice(0);
        this.paramWoSwitchIndex = 0;
        this.params = new Map();
        for (let opt of this.options) {
            this.params.set(opt.name, opt.defaultValue);
        }
        while (this.args.length > 0) {
            this.parseElement();
        }
        return this.params;
    }
    parseElement() {
        var arg = this.args.shift();
        if (arg != "--") {
            if (arg.substr(0, 1) == "-" && arg != "-") {
                let match = /^(--?.+)=(.+)$/.exec(arg);
                if (match) {
                    this.args.unshift(match[2]);
                    arg = match[1];
                }
                for (let opt of this.options) {
                    if (opt.switches != null) {
                        for (let s of opt.switches) {
                            if (s == arg) {
                                this.parseValue(opt, arg);
                                return;
                            }
                        }
                    }
                }
                throw "Unknow switch '" + arg + "'.";
            }
            else {
                this.args.unshift(arg);
                this.parseValue(this.getNextNoSwitchOption(), this.args[0]);
            }
        }
        else {
            while (this.args.length > 0)
                this.parseValue(this.getNextNoSwitchOption(), this.args[0]);
        }
    }
    parseValue(opt, s) {
        this.ensureValueExist(s);
        if (!opt.repeatable)
            this.params.set(opt.name, this.args.shift());
        else
            this.addRepeatableValue(opt.name, this.args.shift());
    }
    hasOption(name) {
        return this.options.find(opt => opt.name == name) != null;
    }
    ensureValueExist(s) {
        if (this.args.length == 0) {
            throw "Missing value after '" + s + "' switch.";
        }
    }
    getNextNoSwitchOption() {
        for (let i = this.paramWoSwitchIndex; i < this.options.length; i++) {
            if (this.options[i].switches == null) {
                if (!this.options[i].repeatable)
                    this.paramWoSwitchIndex = i + 1;
                return this.options[i];
            }
        }
        throw "Unexpected argument '" + this.args[0] + "'.";
    }
    addRepeatableValue(name, value) {
        if (this.params.get(name) == null)
            this.params.set(name, []);
        this.params.get(name).push(value);
    }
    lpad(str, pad, len) {
        while (str.length < len)
            str = pad + str;
        return str;
    }
    rpad(str, pad, len) {
        while (str.length < len)
            str = str + pad;
        return str;
    }
}
exports.CmdOptions = CmdOptions;
//# sourceMappingURL=CmdOptions.js.map