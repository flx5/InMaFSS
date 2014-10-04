<?php
class ScopeData {

    const BASIC = 'basic';
    const SUBSTITUTION_PLAN = 'substitutions';
    const SUBSTITUTION_PLAN_FULL = 'all_substitutions';
    const TEACHER_PLAN_FULL = 'teacher_plan_full';
    const UPDATE_SUBSTITION_PLAN = 'update_substitutions';
    const TICKER = 'ticker';
    const OTHER = 'other';
    const MENSA = 'mensa';
    const UPDATE_MENSA = 'update_mensa';
    const EVENTS = 'events';
    const UPDATE_EVENTS = 'update_events';

    public static $scopes = Array(
        self::BASIC,
        self::SUBSTITUTION_PLAN,
        self::TICKER,
        self::EVENTS,
        self::MENSA
    );
    
    public static $scopesTeacher = Array(
        self::OTHER
    );
    
    public static $scopesSpecial = Array(
        self::SUBSTITUTION_PLAN_FULL,
        self::TEACHER_PLAN_FULL,
        self::UPDATE_SUBSTITION_PLAN,
        self::UPDATE_MENSA,
        self::UPDATE_EVENTS,
    );

    public static function GetScopes() {
        return array_merge(self::$scopes, self::$scopesTeacher, self::$scopesSpecial);
    }
}
?>