function Init(interval, style, limit, isTeacher) {
    Pages.Init(interval);
    Update.init(style, limit, isTeacher);
    new Ticker('ticker');
}