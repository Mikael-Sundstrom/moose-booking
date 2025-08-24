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
        data.append('template_id', document.getElementById('template_id').value);
    
        // 🟢 Viktigt: Skicka med custom_dates som finns i inputfältet
        const customDatesJson = document.getElementById('custom_dates_input').value || '[]';
        data.append('custom_dates', customDatesJson);
    
        try {
            const response = await fetch(moosebooking_ajax.ajax_url, {
                method: 'POST',
                body: data
            });
    
            const responseText = await response.text();
            document.querySelector('.moosebooking-calendar').innerHTML = responseText;
            document.getElementById('calendar-month-year').innerText =
                currentDate.toLocaleString('default', { month: 'long' }) + ' ' + year;
    
            attachDayClickHandlers(); // 🟢 Klicka på dagar ska funka efter att ny kalender laddas
            attachNavigationHandlers(); // 🔵 Navigeringen ska också återanslutas
    
        } catch (error) {
            console.error('Fel vid hämtning av kalender:', error);
        }
    }
    

    function attachDayClickHandlers() {
        document.querySelectorAll('.moosebooking-calendar .day').forEach(day => {
            day.addEventListener('click', () => openDayModal(day));
        });
    }

    function openDayModal(day) {
        if (!day || !day.dataset || !day.dataset.date) return;

        const date = day.dataset.date;
        document.getElementById('custom-day-date').textContent = date;

        let customDates = JSON.parse(document.getElementById('custom_dates_input').value || '[]');
        const existing = customDates.find(d => d.date === date);

        document.getElementById('custom-day-bookable').checked = existing ? existing.bookable : true;

        // Visa modalen
        document.getElementById('custom-day-modal').style.display = 'flex';

        // Spara
        document.getElementById('save-custom-day').onclick = () => {
            const bookable = document.getElementById('custom-day-bookable').checked;
            const updatedDate = {
                date: date,
                bookable: bookable,
                available: []
            };
        
            const index = customDates.findIndex(d => d.date === date);
            if (index !== -1) {
                customDates[index] = updatedDate;
            } else {
                customDates.push(updatedDate);
            }
        
            document.getElementById('custom_dates_input').value = JSON.stringify(customDates);
            document.getElementById('custom-day-modal').style.display = 'none';
        
            // Uppdatera klasser
            day.classList.add('custom-day');
            if (!bookable) {
                day.classList.add('unavailable');
            } else {
                day.classList.remove('unavailable');
            }
        };
        

        document.getElementById('close-custom-day').onclick = () => {
            document.getElementById('custom-day-modal').style.display = 'none';
        };
    }

    // Klick utanför modal-content stänger modalen
    document.getElementById('custom-day-modal').addEventListener('click', (e) => {
        if (e.target.id === 'custom-day-modal') {
            document.getElementById('custom-day-modal').style.display = 'none';
        }
    });

    // Escape stänger modalen
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.getElementById('custom-day-modal').style.display = 'none';
        }
    });

    // -------- NAVIGATION --------
    function attachNavigationHandlers() {
        const prev = document.getElementById('prev-month');
        const next = document.getElementById('next-month');

        if (prev) {
            prev.onclick = () => {
                currentDate = new Date(currentDate.getFullYear(), currentDate.getMonth() - 1, 1);
                renderCalendar(currentDate.getFullYear(), currentDate.getMonth());
            };
        }

        if (next) {
            next.onclick = () => {
                currentDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 1);
                renderCalendar(currentDate.getFullYear(), currentDate.getMonth());
            };
        }
    }

    // Första rendering
    renderCalendar(currentDate.getFullYear(), currentDate.getMonth());

});
