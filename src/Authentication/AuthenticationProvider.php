<?php

/*
 * Copyright (C) 2016 Felix Prasse <me@flx5.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace InMaFSS\Authentication;

/**
 * Base type for authorization providers.
 *
 * @author Felix Prasse <me@flx5.com>
 */
abstract class AuthenticationProvider {
    private $session;
    
    public function __construct(\Aura\Session\Session $session) {
        $this->session = $session;
    }
    
    public abstract function IsValidLogin($username, $password);
    public abstract function HasRight(UserRight $right);
}
