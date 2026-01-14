document.addEventListener('DOMContentLoaded', function () {
    // Real-time Clock
    function updateClock() {
        const now = new Date();
        const clockElement = document.getElementById('realTimeClock');
        if (clockElement) {
            clockElement.textContent = now.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            });
        }
    }

    updateClock();
    setInterval(updateClock, 1000);

    // Check-in/Out UI Mock Logic
    const btnCheckIn = document.getElementById('btnCheckIn');
    const btnCheckOut = document.getElementById('btnCheckOut');
    const checkInStatus = document.getElementById('checkInStatus');
    const statusTitle = document.getElementById('attendanceStatusTitle');
    const recordedIn = document.getElementById('recordedCheckIn');
    const recordedOut = document.getElementById('recordedCheckOut');

    let isCheckedIn = false;

    if (btnCheckIn) {
        btnCheckIn.addEventListener('click', function () {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

            isCheckedIn = true;
            this.disabled = true;
            btnCheckOut.classList.remove('disabled');

            checkInStatus.classList.add('check-in-active', 'border-primary', 'bg-primary', 'bg-opacity-10');
            statusTitle.textContent = 'Checked In';
            statusTitle.classList.add('text-primary');
            recordedIn.textContent = timeStr;
            recordedIn.classList.add('text-primary');

            showAlert(`Successfully checked in at ${timeStr}`, 'success');
        });
    }

    if (btnCheckOut) {
        btnCheckOut.addEventListener('click', function () {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

            isCheckedIn = false;
            this.disabled = true;
            this.classList.add('disabled');

            statusTitle.textContent = 'Checked Out';
            statusTitle.classList.remove('text-primary');
            statusTitle.classList.add('text-danger');
            recordedOut.textContent = timeStr;
            recordedOut.classList.add('text-danger');

            showAlert(`Successfully checked out at ${timeStr}`, 'success');
        });
    }
});
