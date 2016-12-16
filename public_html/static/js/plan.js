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


/* global Ajax */

var PlanType = {
    Pupil: 'pupil',
    Teacher: 'teacher'
};

/**
 * Create a Plan with the given type.
 * @param {PlanType} type
 * @returns {Plan}
 */
function Plan(type) {
    this.data = null;
    this.updateInterval = 60000;

    this.getHeight = function () {
        return window.innerHeight;
    };

    this.getLimit = function () {
        // TODO Revisit & improve
        return Math.floor((this.getHeight() - 50) / 25) - 4;
    };

    this.requestUpdate = function () {
        var self = this;
        Ajax.get("update/" + type + "/" + this.getLimit() + "?t=" + Date.now(),
                function (response, httpStatus) {
                    if (httpStatus === 200) {
                        self.data = JSON.parse(response);

                        self.setCacheWarning(false);
                    } else {
                        self.setCacheWarning(true);
                    }

                    window.setTimeout(function () {
                        self.requestUpdate();
                    }, self.updateInterval);
                }
        );
    };
    
    this.updateContent = function() {
        var left = document.getElementById('plan_left');
        var right = document.getElementById('plan_right');
        
        left.getElementsByClassName('no_content')[0].style.display = 'none';
        right.getElementsByClassName('no_content')[0].style.display = 'none';
    };

    this.setCacheWarning = function (enable) {
        var cache = document.getElementById("header_cached");
        var normal = document.getElementById("header_normal");

        if (enable) {
            cache.style.display = 'inline-block';
            normal.style.display = 'none';
            document.getElementById('header').style.backgroundColor = "#ff0000";
        } else {
            cache.style.display = 'none';
            normal.style.display = 'inline-block';
            document.getElementById('header').style.backgroundColor = "";
        }
    };

    this.enableCacheWarning = function () {
        var t = true;
        var self = this;

        window.setInterval(function () {
            self.setCacheWarning(t);
            t = !t;
        }, 2500);
    };

    this.requestUpdate();
}