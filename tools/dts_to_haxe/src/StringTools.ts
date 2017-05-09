export class StringTools
{
    static capitalize(s:string) : string
    {
        return s.charAt(0).toUpperCase() + s.slice(1);
    }

    static decapitalize(s:string) : string
    {
        return s.charAt(0).toLowerCase() + s.slice(1);
    }
}