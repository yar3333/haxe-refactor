import * as fs from "fs";
import * as path from "path";

export type WalkSyncCallback = (start:string, dirs:Array<string>, files:Array<string>) => void;

export function walkSync(start:string, callback:WalkSyncCallback) : void
{
    var stat = fs.statSync(start);

    if (stat.isDirectory())
    {
        var filenames = fs.readdirSync(start);

        var coll = filenames.reduce(function (acc, name)
        {
            var abspath = path.join(start, name);

            if (fs.statSync(abspath).isDirectory())
            {
                acc.dirs.push(name);
            }
            else
            {
                acc.names.push(name);
            }

            return acc;
        }, {"names": [], "dirs": []});

        callback(start, coll.dirs, coll.names);

        coll.dirs.forEach(function (d)
        {
            var abspath = path.join(start, d);
            walkSync(abspath, callback);
        });
    }
    else
    {
        throw new Error("path: " + start + " is not a directory");
    }
}

export function mkdirp(p, mode?, made?)
{
    if (mode === undefined) mode = parseInt('0777', 8) & (~process.umask());
    if (!made) made = null;

    p = path.resolve(p);

    try
    {
        fs.mkdirSync(p, mode);
        made = made || p;
    }
    catch (err0)
    {
        switch (err0.code)
        {
            case 'ENOENT' :
                made = mkdirp(path.dirname(p), mode, made);
                mkdirp(p, mode, made);
                break;

            // In the case of any other error, just see if there's a dir
            // there already.  If so, then hooray!  If not, then something
            // is borked.
            default:
                var stat;
                try { stat = fs.statSync(p); }
                catch (err1) { throw err0; }
                if (!stat.isDirectory()) throw err0;
                break;
        }
    }

    return made;
}

export function readTextFileLines(fileName:string, trim:boolean, ignoreEmptyLinesAndComments:boolean) : Array<string>
{
    var lines = fs.readFileSync(fileName).toString().split("\r\n").join("\n").split("\r").join("\n").split("\n");
    if (trim) lines = lines.filter(x => x.trim());
    if (ignoreEmptyLinesAndComments) lines = lines.filter(x => x !== "" && !x.startsWith("#") && !x.startsWith("//"));
    return lines;
}