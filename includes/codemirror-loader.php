<?php if (!defined('ABSPATH')) exit;

/**
 * codemirror-loader.php
 * Enqueue CodeMirror scripts and styles dynamically.
 */

/**
 * Enqueue CodeMirror scripts and styles dynamically.
 *
 * Loads CodeMirror resources only when needed for the CSS and Scripts tabs.
 *
 * @return void
 */
add_action('admin_enqueue_scripts', function () {

	// Only load for the CSS and Script tabs
	$allowed_tabs = ['css', 'scripts'];

	if (!in_array(moosebooking_get_current_tab(), $allowed_tabs, true)) {
		return;
	}

    // Enqueue CodeMirror only if needed
    wp_enqueue_code_editor([]);
    wp_enqueue_script('code-editor');

    // Add inline script to initialize CodeMirror
    wp_add_inline_script(
        'code-editor',
        '
        document.addEventListener("DOMContentLoaded", function () {
            const textareas = document.querySelectorAll(".moosebooking-code-editor");

            textareas.forEach((textarea) => {
                const mode = textarea.dataset.mode || "plaintext"; // Default to plaintext if mode is missing

                wp.codeEditor.initialize(textarea, {
                    codemirror: {
                        mode: mode, // Use mode from data-mode attribute
                        lineNumbers: true,
                        autoCloseBrackets: true,
                        indentUnit: 2,
                        tabSize: 2,
						viewportMargin: Infinity,
                    }
                });

                console.log(`[Admin Tweak Suite] CodeMirror initialized for ${textarea.id} with mode: ${mode}`);
            });
        });
        '
    );
});
