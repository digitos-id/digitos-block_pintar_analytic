// Standard license block omitted.
/*
 * @module     block_overview/helloworld
 * @copyright  2015 Someone cool
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as Str from 'core/str';

/**
 * Reveal all of the hidden notes.
 */
const showAllNotes = () => {
    document.querySelectorAll('.note.hidden').map(note => note.removeClass('hidden'));
};

/**
 * Hide all of the notes.
 */
const hideAllNotes = () => document.querySelectorAll('.note').map(note => note.addClass('hidden'));

/**
 * Return a personalised, formal, greeting.
 *
 * @param   {String} name The name of the person to greet
 * @returns {Promise}
 */
export const formal = name => Str.get_string('formallygreet', 'block_overview', name);

/**
 * Return a personalised, informal, greeting.
 *
 * @param   {String} name The name of the person to greet
 * @returns {Promise}
 */
export const informal = name => {
    return Str.get_string('informallygreet', 'block_overview', name);
};
