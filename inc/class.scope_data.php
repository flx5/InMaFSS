<?php
class ScopeData {

    const BASIC = 'basic';
    const SUBSTITUTION_PLAN = 'substitutions';
    const SUBSTITUTION_PLAN_FULL = 'all_substitutions';
    const TEACHER_PLAN_FULL = 'teacher_plan_full';
    const UPDATE_SUBSTITION_PLAN = 'update_substitutions';
    const TICKER = 'ticker';
    const OTHER = 'other';
    const UPDATE_MENSA = 'update_mensa';

    public static $scopes = Array(
        self::BASIC,
        self::SUBSTITUTION_PLAN,
        self::TICKER,
        self::UPDATE_MENSA
    );
    
    public static $scopesTeacher = Array(
        self::OTHER
    );
    
    public static $scopesSpecial = Array(
        self::SUBSTITUTION_PLAN_FULL,
        self::TEACHER_PLAN_FULL,
        self::UPDATE_SUBSTITION_PLAN,
        self::UPDATE_MENSA
    );

    public static function GetScopes() {
        return array_merge(self::$scopes, self::$scopesTeacher, self::$scopesSpecial);
    }
}
?>