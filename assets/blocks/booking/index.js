/**
 * Moose Booking Gutenberg Block
 * Renderar shortcode [moosebooking id="X" eller template="namn"]
 */

import { registerBlockType } from "@wordpress/blocks";
import { InspectorControls } from "@wordpress/block-editor";
import {
	PanelBody,
	SelectControl,
	Spinner,
	Notice,
} from "@wordpress/components";
import { useState, useEffect } from "@wordpress/element";
import apiFetch from "@wordpress/api-fetch";

registerBlockType("moose-booking/booking", {
	title: "Moose Booking",
	description:
		"Visa ett bokningsformulär eller kalender från en vald template.",
	icon: "calendar-alt",
	category: "common",
	attributes: {
		id: { type: "number", default: 0 },
		template: { type: "string", default: "" },
	},

	edit: ({ attributes, setAttributes }) => {
		const [templates, setTemplates] = useState([]);
		const [loading, setLoading] = useState(true);
		const [error, setError] = useState("");

		// 🔹 Hämta templates via REST API
		useEffect(() => {
			let isMounted = true;

			apiFetch({ path: "/moosebooking/v1/templates" })
				.then((data) => {
					if (!isMounted) return;
					setTemplates(data || []);
				})
				.catch((err) => {
					if (!isMounted) return;
					console.error("MooseBooking REST error:", err);
					setError("Kunde inte hämta templates.");
				})
				.finally(() => {
					if (isMounted) setLoading(false);
				});

			return () => {
				isMounted = false;
			};
		}, []);

		// 🔹 Sidopanel i blockeditorn
		const inspector = (
			<InspectorControls>
				<PanelBody title="Välj bokningsmall" initialOpen={true}>
					{loading && <Spinner />}
					{error && <Notice status="error">{error}</Notice>}
					{!loading && !error && (
						<SelectControl
							label="Aktiv template"
							value={attributes.id}
							options={[
								{ label: "– Välj template –", value: 0 },
								...templates.map((tpl) => ({
									label: tpl.name,
									value: tpl.id,
								})),
							]}
							onChange={(value) => {
								const id = parseInt(value, 10);
								const tpl = templates.find((t) => t.id === id);
								setAttributes({
									id,
									template: tpl ? tpl.name : "",
								});
							}}
						/>
					)}
				</PanelBody>
			</InspectorControls>
		);

		// 🔹 Förhandsvisning i editorn
		return (
			<div className="moosebooking-block">
				{inspector}
				{attributes.id ? (
					<div className="moosebooking-preview">
						<p>
							<strong>Förhandsvisning:</strong>{" "}
							{attributes.template ||
								`Template #${attributes.id}`}
						</p>
						<p style={{ opacity: 0.6 }}>
							Bokningskalendern visas här på frontend.
						</p>
					</div>
				) : (
					<p>Välj en template i sidopanelen.</p>
				)}
			</div>
		);
	},

	// 🔹 Frontend-rendering via PHP (shortcode)
	save: ({ attributes }) => {
		if (attributes.id) {
			return `[moosebooking id="${attributes.id}"]`;
		}
		if (attributes.template) {
			return `[moosebooking template="${attributes.template}"]`;
		}
		return "";
	},
});
