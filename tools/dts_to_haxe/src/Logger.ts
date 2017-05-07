import { ILogger } from "./ILogger";

export class Logger implements ILogger
{
    private warnCounter = 0;

    constructor(private level:"none"|"warn"|"debug") {}

    log(s:any) : void
    {
        if (this.level == "none") return;
        if (this.level == "warn")
        {
            if (this.warnCounter > 0) console.warn(s);
        }
        else
        {
            if (this.warnCounter > 0) console.warn(s);
            else                      console.log(s);
        }
    }

    beginWarn() : void
    {
        this.warnCounter++;
    }

    endWarn() : void
    {
        this.warnCounter--;
    }
}