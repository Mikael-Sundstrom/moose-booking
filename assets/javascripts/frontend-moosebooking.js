document.addEventListener("DOMContentLoaded", () => {
	console.log("🦌 Moose Booking frontend loaded");
	document.querySelectorAll(".moosebooking-widget").forEach((el) => {
		console.log("Loaded widget for template:", el.dataset.template);
	});
});
