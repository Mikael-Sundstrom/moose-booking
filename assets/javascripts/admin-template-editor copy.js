console.log('Admin Template Editor JS loaded!');

document.addEventListener('DOMContentLoaded', () => {

    let currentDate = new Date();

    // -------- KALENDERRENDERING --------
    async function renderCalendar(year, month) {
        const data = new FormData();
        data.append('action', 'moosebooking_generate_calendar');
        data.append('year', year);
        data.append('month', month + 1);
        data.append('nonce', moosebooking_ajax.nonce);
    
        // 🟢 NYTT: Skicka med template_id
        data.append('template_id', document.getElementById('template_id').value);
    
        try {
            const response = await fetch(moosebooking_ajax.ajax_url, {
                method: 'POST',
                body: data
            });
    
            const responseText = await response.text();
            document.querySelector('.moosebooking-calendar').innerHTML = responseText;
            document.getElementById('calendar-month-year').innerText =
                currentDate.toLocaleString('default', { month: 'long' }) + ' ' + year;
    
        } catch (error) {
            console.error('Fel vid hämtning av kalender:', error);
        }
    }
    
    // Navigering
    document.getElementById('prev-month').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar(currentDate.getFullYear(), currentDate.getMonth());
    });

    document.getElementById('next-month').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar(currentDate.getFullYear(), currentDate.getMonth());
    });

    function attachNavigationHandlers() {
        const prev = document.getElementById('prev-month');
        const next = document.getElementById('next-month');
        if (!prev || !next) return;
    
        prev.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar(currentDate.getFullYear(), currentDate.getMonth());
        });
    
        next.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar(currentDate.getFullYear(), currentDate.getMonth());
        });
    }
    

    // Första rendering
    renderCalendar(currentDate.getFullYear(), currentDate.getMonth());

    // -------- KLICKFUNKTIONER PÅ DAGAR --------
    document.querySelectorAll('.moosebooking-calendar .day').forEach(day => {
        day.addEventListener('click', () => {
            const date = day.dataset.date;
            if (!date) return;
    
            // Visa datum i dialogen
            document.getElementById('custom-day-date').textContent = date;
    
            // Läs in befintlig JSON från hidden input
            let customDates = JSON.parse(document.getElementById('custom_dates_input').value || '[]');
    
            // Finns datumet redan?
            const existing = customDates.find(d => d.date === date);
    
            // Fyll i dialogens fält
            document.getElementById('custom-day-bookable').checked = existing ? existing.bookable : true;
    
            // TODO: Lägg till befintliga tider i #custom-day-times om de finns
    
            // Visa modalen
            document.getElementById('custom-day-modal').style.display = 'block';
    
            // Spara knapp
            document.getElementById('save-custom-day').onclick = () => {
                const bookable = document.getElementById('custom-day-bookable').checked;
    
                // TODO: Läs av tider från #custom-day-times
    
                const updatedDate = {
                    date: date,
                    bookable: bookable,
                    available: [] // just nu tom, bygga sen
                };
    
                // Uppdatera eller lägg till datumet
                const index = customDates.findIndex(d => d.date === date);
                if (index !== -1) {
                    customDates[index] = updatedDate;
                } else {
                    customDates.push(updatedDate);
                }
    
                // Uppdatera hidden input
                document.getElementById('custom_dates_input').value = JSON.stringify(customDates);
    
                // Dölj modalen
                document.getElementById('custom-day-modal').style.display = 'none';
    
                // Valfritt: markera dagen som custom i kalendern
                day.classList.add('custom-day');
            };
    
            // Stäng-knapp
            document.getElementById('close-custom-day').onclick = () => {
                document.getElementById('custom-day-modal').style.display = 'none';
            };
        });
    });
    

    function addDateToInput(date) {
        let dates = document.getElementById('unavailable_dates').value.split(',').filter(Boolean);
        if (!dates.includes(date)) {
            dates.push(date);
            dates.sort(); // Håll ordning
        }
        document.getElementById('unavailable_dates').value = dates.join(',');
    }

    function removeDateFromInput(date) {
        let dates = document.getElementById('unavailable_dates').value.split(',').filter(d => d !== date);
        document.getElementById('unavailable_dates').value = dates.join(',');
    }

    // -------- TIDSSLOTTAR --------
    document.getElementById('add-time-slot').addEventListener('click', () => {
        const container = document.getElementById('moosebooking-time-slots');
        const slot = document.createElement('div');
        slot.classList.add('time-slot');
        slot.innerHTML = `
            <label>Start time:</label>
            <input type="time" name="standard_start_times[]" value="">
            <label>End time:</label>
            <input type="time" name="standard_end_times[]" value="">
            <button type="button" class="remove-slot button">×</button>
        `;
        container.appendChild(slot);
    });

    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-slot')) {
            e.target.parentElement.remove();
        }
    });

    // -------- PRISSÄTTNING --------
    document.getElementById('add-pricing-slot').addEventListener('click', () => {
        const container = document.getElementById('moosebooking-pricing-slots');
        const slot = document.createElement('div');
        slot.classList.add('pricing-slot');
        slot.innerHTML = `
            <label>${moosebooking_strings.type}:</label>
            <select name="pricing_type[]">
                <option value="hourly">${moosebooking_strings.hourly}</option>
                <option value="daily">${moosebooking_strings.daily}</option>
                <option value="weekend">${moosebooking_strings.weekend}</option>
                <option value="weekly">${moosebooking_strings.weekly}</option>
                <option value="custom">${moosebooking_strings.custom}</option>
            </select>
            <label>${moosebooking_strings.price}:</label>
            <input type="number" name="pricing_amount[]" step="0.01" value="">
            <label>${moosebooking_strings.comment}:</label>
            <input type="text" name="pricing_comment[]" value="">
            <button type="button" class="remove-pricing-slot button">${moosebooking_strings.remove}</button>
        `;
        container.appendChild(slot);
    });

    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-pricing-slot')) {
            e.target.parentElement.remove();
        }
    });

    function attachDayClickHandlers() {
        document.querySelectorAll('.moosebooking-calendar .day').forEach(day => {
            day.addEventListener('click', () => {
                const date = day.dataset.date;
                if (!date) return;
    
                document.getElementById('custom-day-date').textContent = date;
    
                let customDates = JSON.parse(document.getElementById('custom_dates_input').value || '[]');
    
                const existing = customDates.find(d => d.date === date);
    
                document.getElementById('custom-day-bookable').checked = existing ? existing.bookable : true;
    
                document.getElementById('custom-day-modal').style.display = 'block';
    
                document.getElementById('save-custom-day').onclick = () => {
                    const bookable = document.getElementById('custom-day-bookable').checked;
    
                    const updatedDate = {
                        date: date,
                        bookable: bookable,
                        available: [] // TODO
                    };
    
                    const index = customDates.findIndex(d => d.date === date);
                    if (index !== -1) {
                        customDates[index] = updatedDate;
                    } else {
                        customDates.push(updatedDate);
                    }
    
                    document.getElementById('custom_dates_input').value = JSON.stringify(customDates);
    
                    document.getElementById('custom-day-modal').style.display = 'none';
                    day.classList.add('custom-day');
                };
    
                document.getElementById('close-custom-day').onclick = () => {
                    document.getElementById('custom-day-modal').style.display = 'none';
                };
            });
        });
    }
});