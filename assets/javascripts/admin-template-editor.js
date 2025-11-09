/**
 * Moose Booking – Admin Template Editor
 * Hanterar veckostandarder, tider, priser, JSON-synk och custom limits.
 */
console.log("Admin Template Editor JS loaded (enhanced version)");

document.addEventListener("DOMContentLoaded", () => {
	/* ============================================================
	   🕒 SEKTION 1: LÄGG TILL & TA BORT SLOT-RADER
	============================================================ */
	document.querySelectorAll(".add-slot").forEach((btn) => {
		btn.addEventListener("click", () => {
			const day = btn.dataset.day;

			const timeContainer = document.querySelector(
				`.slots-list[data-day="${day}"]`
			);
			const priceContainer = document.querySelector(
				`.slots-list[data-day="${day}-price"]`
			);
			if (!timeContainer || !priceContainer) return;

			const index = timeContainer.querySelectorAll(".slot-row").length;

			// Lägg till tider
			const timeRow = document.createElement("div");
			timeRow.classList.add("slot-row");
			timeRow.innerHTML = `
				<input type="time" name="weekly_defaults[${day}][slots][${index}][start]" value="08:00">
				<span>–</span>
				<input type="time" name="weekly_defaults[${day}][slots][${index}][end]" value="17:00">
				<button type="button" class="button-link remove-slot">×</button>
			`;
			timeContainer.appendChild(timeRow);

			// Lägg till pris
			const priceRow = document.createElement("div");
			priceRow.classList.add("slot-row");
			priceRow.innerHTML = `
				<input type="number" step="0.01" min="0"
					name="weekly_defaults[${day}][slots][${index}][price]" value="0"
					placeholder="0" style="width:80px;">
			`;
			priceContainer.appendChild(priceRow);

			updateBookableCheckboxStates();
			syncWeeklyDefaultsToJson();
		});
	});

	document.addEventListener("click", (e) => {
		if (e.target.classList.contains("remove-slot")) {
			const timeRow = e.target.closest(".slot-row");
			if (!timeRow) return;

			const slotsList = timeRow.parentElement;
			const day = slotsList.dataset.day;
			const index = Array.from(slotsList.children).indexOf(timeRow);

			timeRow.remove();

			const priceList = document.querySelector(
				`.slots-list[data-day="${day}-price"]`
			);
			if (priceList && priceList.children[index]) {
				priceList.children[index].remove();
			}

			updateBookableCheckboxStates();
			syncWeeklyDefaultsToJson();
		}
	});

	/* ============================================================
	   🔁 SEKTION 2: JSON-SYNK FÖR VECKOVISA STANDARDER
	============================================================ */
	function syncWeeklyDefaultsToJson() {
		const defaults = {};
		document.querySelectorAll("tr[data-day]").forEach((row) => {
			const day = row.dataset.day;
			const bookable = row.querySelector(
				"input[type='checkbox']"
			)?.checked;
			const slots = [];

			const timeRows = row.querySelectorAll(
				`.slots-list[data-day='${day}'] .slot-row`
			);
			timeRows.forEach((timeRow, i) => {
				const start =
					timeRow.querySelector("input[name*='[start]']")?.value ||
					"";
				const end =
					timeRow.querySelector("input[name*='[end]']")?.value || "";
				const priceInput = row.querySelectorAll(
					`.slots-list[data-day='${day}-price'] .slot-row input`
				)[i];
				const price = parseFloat(priceInput?.value || 0);
				slots.push({ start, end, price });
			});

			defaults[day] = { bookable, slots };
		});

		document.getElementById("weekly_defaults_json").value =
			JSON.stringify(defaults);
	}

	function updateBookableCheckboxStates() {
		document.querySelectorAll("tr[data-day]").forEach((row) => {
			const day = row.dataset.day;
			const checkbox = row.querySelector("input[type='checkbox']");
			const timeRows = row.querySelectorAll(
				`.slots-list[data-day='${day}'] .slot-row`
			);

			if (!checkbox) return;

			if (timeRows.length === 0) {
				checkbox.checked = false;
				checkbox.disabled = true;
			} else {
				checkbox.disabled = false;
			}
		});
	}

	/* ============================================================
	   ⚙️ SEKTION 3: HANTERA CUSTOM LIMITS (override_limits)
	============================================================ */
	const overrideCheckbox = document.getElementById("override_limits");
	const maxDaysField = document.getElementById("max_days_ahead");
	const minHoursField = document.getElementById("min_hours_before");

	// ⚠️ Gör att "Max dagar" och "Min timmar" är gråade tills man bockar i override.
	function toggleLimitFields() {
		if (!overrideCheckbox) return;

		const disabled = !overrideCheckbox.checked;
		[maxDaysField, minHoursField].forEach((field) => {
			if (field) {
				field.disabled = disabled;
				field.closest("tr").style.opacity = disabled ? "0.5" : "1";

				// Visa globalt värde om inaktiv, annars lokalt
				const globalVal = field.dataset.globalValue;
				const localVal = field.dataset.localValue;
				field.value = disabled ? globalVal : localVal;
			}
		});
	}

	if (overrideCheckbox) {
		overrideCheckbox.addEventListener("change", toggleLimitFields);
		toggleLimitFields(); // Init vid laddning
	}

	/* ============================================================
	   💾 SEKTION 4: JSON-UPPDATERING VID ÄNDRING
	============================================================ */
	document.addEventListener("change", (e) => {
		// Hoppa över override-checkboxen (den styr bara UI)
		if (e.target.id === "override_limits") return;

		if (
			e.target.matches("input[type='checkbox']") ||
			e.target.matches("input[type='time']") ||
			e.target.matches("input[type='number']")
		) {
			syncWeeklyDefaultsToJson();
		}
	});

	// Init vid sidladdning
	syncWeeklyDefaultsToJson();
	updateBookableCheckboxStates();
});
