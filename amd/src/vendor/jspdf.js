// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * jsPDF AMD wrapper
 * jsPDF is loaded via externaljs, so we wrap the global variable
 *
 * @copyright   2021 wisdmlabs <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([], function() {
    // jsPDF is loaded globally via externaljs
    if (typeof window !== 'undefined' && typeof window.jsPDF !== 'undefined') {
        return window.jsPDF;
    }
    // Fallback: return a dummy function if jsPDF is not available
    return function() {
        console.warn('jsPDF is not loaded');
        return null;
    };
});

