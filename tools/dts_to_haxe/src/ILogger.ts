export interface ILogger
{
    log(s:any) : void;
    beginWarn() : void;
    endWarn() : void;
}