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

namespace InMaFSS\Controller;

/**
 * Description of PlanController
 *
 * @author Felix Prasse <me@flx5.com>
 */
class PlanController extends \SlimController\SlimController {

    public function indexAction() {
        // TODO Permissions
        
        $this->render('plan/index', array(
            'ticker' => array('test'),
            'schoolname' => 'TODO'
        ));
    }

    public function updateAction($type, $limit) {
        
         // TODO Permissions
        
        $data = Array();
        
        $viewType = \InMaFSS\ReplacementsTypes::fromName($type);
        
        $query = \InMaFSS\Data\ReplacementQuery::create();
        
        $today = $query->findByDay(new \DateTime("today"));
        $tomorrow = $query->findByDay(new \DateTime("tomorrow"));
        
        foreach($today as $replacement) {
            var_dump($replacement);
        }
        
        //$view_left = new view('left', $limit, $viewType);
    }
}
