function custom_dropdown() {
    const dropdown = document.getElementById('arkDropdown');
    const dropdownButton = dropdown.querySelector('.ark__dropdown-button');

    dropdownButton.addEventListener('click', (e) => {
        e.stopPropagation();
        dropdown.classList.toggle('ark__active');
    });

    document.addEventListener('click', () => {
        dropdown.classList.remove('ark__active');
    });

    const dropdownItems = dropdown.querySelectorAll('.ark__dropdown-item');
    const mainFlag = dropdownButton.querySelector('.ark__flag img');

    dropdownItems.forEach(item => {
        item.addEventListener('click', (e) => {
            e.stopPropagation();

            // Get selected flag image
            const selectedFlag = item.querySelector('.ark__flag img');

            // Update main button flag
            mainFlag.src = selectedFlag.src;
            mainFlag.alt = selectedFlag.alt;

            // Close dropdown
            dropdown.classList.remove('ark__active');
        });
    });
}
custom_dropdown()

// function smartWizard() {
//     let currentStep = 1;
//     const totalSteps = 4;

//     const nextBtn = document.getElementById('nextBtn');
//     const backBtn = document.getElementById('backBtn');
//     const wizardSteps = document.querySelectorAll('.wizard-step');
//     const formFields = document.querySelectorAll('.form-fields');

//     function validateStep(step) {
//         const fields = document.querySelectorAll(`#step${step}-fields input, #step${step}-fields select`);
//         let isValid = true;

//         fields.forEach(field => {
//             const formGroup = field.closest('.form-group');
//             if (!field.value.trim()) {
//                 formGroup.classList.add('error');
//                 isValid = false;
//             } else {
//                 formGroup.classList.remove('error');
//             }
//         });

//         return isValid;
//     }

//     function updateStepDisplay() {
//         wizardSteps.forEach((step, index) => {
//             const stepNum = index + 1;
//             if (stepNum < currentStep) {
//                 step.classList.add('completed');
//                 step.classList.remove('active');
//             } else if (stepNum === currentStep) {
//                 step.classList.add('active');
//                 step.classList.remove('completed');
//             } else {
//                 step.classList.remove('active', 'completed');
//             }
//         });

//         formFields.forEach((field, index) => {
//             if (index + 1 === currentStep) {
//                 field.classList.add('active');
//             } else {
//                 field.classList.remove('active');
//             }
//         });

//         backBtn.disabled = currentStep === 1;
//         // nextBtn.textContent = currentStep === totalSteps ? `Submit` : 'Next';
//         if (currentStep === totalSteps) {
//             nextBtn.innerHTML = `<p>Submit</p> <img src="/assets/images/next-arrow.svg" alt="Submit">`;
//           } else {
//             nextBtn.innerHTML = `<p>Next</p> <img src="/assets/images/next-arrow.svg" alt="Next">`;
//           }
//     }

//     // nextBtn.addEventListener('click', () => {
//     //     if (validateStep(currentStep)) {
//     //         if (currentStep < totalSteps) {
//     //             currentStep++;
//     //             updateStepDisplay();
//     //         } else {
//     //             alert('Form submitted successfully!');
//     //         }
//     //     }
//     // });

//     nextBtn.addEventListener('click', () => {
//         if (validateStep(currentStep)) {
//           if (currentStep < totalSteps) {
//             currentStep++;
//             updateStepDisplay();
//           } else {
//             // 👇 Show popup instead of alert
//             const popup = document.getElementById('successPopup');
//             popup.classList.add('active');
//             setTimeout(() => popup.classList.remove('active'), 4000);
//           }
//         }
//       });
      
//       // Close popup
//       document.getElementById('closePopup').addEventListener('click', () => {
//         document.getElementById('successPopup').classList.remove('active');
//       });

//     backBtn.addEventListener('click', () => {
//         if (currentStep > 1) {
//             currentStep--;
//             updateStepDisplay();
//         }
//     });

//     // Remove error state on input
//     document.querySelectorAll('input, select').forEach(field => {
//         field.addEventListener('input', () => {
//             const formGroup = field.closest('.form-group');
//             if (field.value.trim()) {
//                 formGroup.classList.remove('error');
//             }
//         });
//     });

//     updateStepDisplay();
// }
// smartWizard()

function NotificationLogic() {
    // Notification Logic
    const notificationContainer = document.getElementById('arkNotification');
    const notificationButton = notificationContainer.querySelector('.ark__notification-button');
    const notificationContent = document.getElementById('arkNotificationContent');
    const notificationBadge = document.getElementById('arkNotificationBadge');
    const clearButton = document.getElementById('arkClearNotifications');

    // Sample notifications data
    let notifications = [{
            id: 1,
            title: 'New Message',
            message: 'You have received a new message from John',
            time: '5 min ago',
            unread: true
        },
        {
            id: 2,
            title: 'System Update',
            message: 'Your system has been updated successfully',
            time: '1 hour ago',
            unread: true
        },
        {
            id: 3,
            title: 'Welcome!',
            message: 'Welcome to our platform. Get started now!',
            time: '2 hours ago',
            unread: true
        }
    ];

    function updateNotificationBadge() {
        const unreadCount = notifications.filter(n => n.unread).length;
        if (unreadCount > 0) {
            notificationBadge.textContent = unreadCount;
            notificationBadge.classList.remove('ark__hidden');
        } else {
            notificationBadge.classList.add('ark__hidden');
        }
    }

    function renderNotifications() {
        if (notifications.length === 0) {
            notificationContent.innerHTML = `
                    <div class="ark__notification-empty">
                        <svg class="ark__notification-empty-icon" viewBox="0 0 24 24">
                            <path fill="#666" d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                        </svg>
                        <div class="ark__notification-empty-text">No notifications yet</div>
                    </div>
                `;
        } else {
            const notificationHTML = notifications.map(notification => `
                    <div class="ark__notification-item ${notification.unread ? 'ark__unread' : ''}" data-id="${notification.id}">
                        <div class="ark__notification-item-icon">
                            <svg width="19" height="16" viewBox="0 0 19 16" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M1.40186 9.40234L5.63715 14.4023L17.4019 1.40234" stroke="#747474" stroke-width="2.80364" stroke-linecap="round" stroke-linejoin="round"/>
</svg>

                        </div>
                        <div class="ark__notification-item-content">
                            <div class="ark__notification-item-title">${notification.title}</div>
                            <div class="ark__notification-item-message">${notification.message}</div>
                            <div class="ark__notification-item-time">${notification.time}</div>
                        </div>
                    </div>
                `).join('');

            notificationContent.innerHTML = `<div class="ark__notification-list">${notificationHTML}</div>`;

            // Add click event to mark as read
            const notificationItems = notificationContent.querySelectorAll('.ark__notification-item');
            notificationItems.forEach(item => {
                item.addEventListener('click', () => {
                    const id = parseInt(item.dataset.id);
                    const notification = notifications.find(n => n.id === id);
                    if (notification) {
                        notification.unread = false;
                        item.classList.remove('ark__unread');
                        updateNotificationBadge();
                    }
                });
            });
        }
        updateNotificationBadge();
    }

    notificationButton.addEventListener('click', (e) => {
        e.stopPropagation();
        notificationContainer.classList.toggle('ark__active');
        // Close dropdown if open
        // dropdown.classList.remove('ark__active');
        renderNotifications();
    });

    clearButton.addEventListener('click', () => {
        notifications = [];
        renderNotifications();
    });

    // Close both dropdowns when clicking outside
    document.addEventListener('click', () => {
        notificationContainer.classList.remove('ark__active');
    });

    // Initial render
    updateNotificationBadge();
}
NotificationLogic()

function profile_view() {
    const arkProfileBtn = document.getElementById('arkProfileBtn');
    const arkProfileMenu = document.getElementById('arkProfileMenu');
    const arkChevron = document.getElementById('arkChevron');

    arkProfileBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        arkProfileMenu.classList.toggle('ark__open');
        // arkChevron.classList.toggle('ark__open');
    });

    document.addEventListener('click', (e) => {
        if (!arkProfileBtn.contains(e.target) && !arkProfileMenu.contains(e.target)) {
            arkProfileMenu.classList.remove('ark__open');
            // arkChevron.classList.remove('ark__open');
        }
    });

    const arkProfileItems = document.querySelectorAll('.ark__profile-item');
    arkProfileItems.forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            console.log('Clicked:', item.querySelector('span').textContent);
            arkProfileMenu.classList.remove('ark__open');
            arkChevron.classList.remove('ark__open');
        });
    });
}
profile_view()

function arrowRotate() {
    // Select all dropdown wrappers
    const selectWrappers = document.querySelectorAll('.select-wrapper');

    selectWrappers.forEach(wrapper => {
        const select = wrapper.querySelector('select');

        // Toggle rotation on click
        select.addEventListener('click', (e) => {
            e.stopPropagation(); // Prevent bubbling
            // Close all others before opening this one
            selectWrappers.forEach(w => {
                if (w !== wrapper) w.classList.remove('active');
            });
            wrapper.classList.toggle('active');
        });
    });

    // When clicking outside → close all
    document.addEventListener('click', () => {
        selectWrappers.forEach(wrapper => wrapper.classList.remove('active'));
    });


}
arrowRotate()

function custom_DatePicker() {
    const dateInputContainer = document.getElementById('dateInputContainer');
    if (!dateInputContainer) return;
    const dateDisplay = document.getElementById('dateDisplay');
    const shipmentDateInput = document.getElementById('shipment_date');
    const calendarPopup = document.getElementById('calendarPopup');
    const calendarGrid = document.getElementById('calendarGrid');
    const calendarTitle = document.getElementById('calendarTitle');
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');
    const errorMessage = document.getElementById('errorMessage');

    let currentDate = new Date();
    let selectedDate = null;

    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];

    function formatDate(date) {
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const year = date.getFullYear();
        return `${month}/${day}/${year}`;
    }

    function renderCalendar() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();

        calendarTitle.textContent = `${monthNames[month]} ${year}`;

        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const daysInPrevMonth = new Date(year, month, 0).getDate();

        calendarGrid.innerHTML = '';

        const dayHeaders = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];
        dayHeaders.forEach(day => {
            const header = document.createElement('div');
            header.className = 'calendar-day-header';
            header.textContent = day;
            calendarGrid.appendChild(header);
        });

        for (let i = firstDay - 1; i >= 0; i--) {
            const day = document.createElement('div');
            day.className = 'calendar-day disabled';
            day.textContent = daysInPrevMonth - i;
            calendarGrid.appendChild(day);
        }

        const today = new Date();
        for (let i = 1; i <= daysInMonth; i++) {
            const day = document.createElement('div');
            day.className = 'calendar-day';
            day.textContent = i;

            const dayDate = new Date(year, month, i);

            if (dayDate.toDateString() === today.toDateString()) {
                day.classList.add('today');
            }

            if (selectedDate && dayDate.toDateString() === selectedDate.toDateString()) {
                day.classList.add('selected');
            }

            day.addEventListener('click', () => selectDate(dayDate));
            calendarGrid.appendChild(day);
        }

        const remainingDays = 42 - (firstDay + daysInMonth);
        for (let i = 1; i <= remainingDays; i++) {
            const day = document.createElement('div');
            day.className = 'calendar-day disabled';
            day.textContent = i;
            calendarGrid.appendChild(day);
        }
    }

    function selectDate(date) {
        selectedDate = date;
        dateDisplay.textContent = formatDate(date);
        dateDisplay.classList.remove('placeholder');

        const isoDate = date.toISOString().split('T')[0];
        shipmentDateInput.value = isoDate;

        dateInputContainer.classList.remove('error');
        errorMessage.classList.remove('show');

        calendarPopup.classList.remove('show');
        dateInputContainer.classList.remove('focused');
    }

    dateInputContainer.addEventListener('click', (e) => {
        e.stopPropagation();
        const isOpen = calendarPopup.classList.contains('show');

        if (isOpen) {
            calendarPopup.classList.remove('show');
            dateInputContainer.classList.remove('focused');
        } else {
            calendarPopup.classList.add('show');
            dateInputContainer.classList.add('focused');
            renderCalendar();
        }
    });

    prevMonthBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    });

    nextMonthBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    });

    document.addEventListener('click', (e) => {
        if (!calendarPopup.contains(e.target) && !dateInputContainer.contains(e.target)) {
            calendarPopup.classList.remove('show');
            dateInputContainer.classList.remove('focused');
        }
    });

    shipmentDateInput.addEventListener('blur', () => {
        if (!shipmentDateInput.value) {
            dateInputContainer.classList.add('error');
            errorMessage.classList.add('show');
        }
    });

    renderCalendar();
}
custom_DatePicker()

function custom__inputDropdown () {
    $("#pickLocation").select2({
        placeholder: "Select pickup location type",
        allowClear: true
    });
    $("#droplocation").select2({
        placeholder: "Select drop location type",
        allowClear: true
    });
    $("#country").select2({
        placeholder: "Enter pickup city",
        allowClear: true
    });
    $("#deliveryCountry").select2({
        placeholder: "Enter pickup city",
        allowClear: true
    });
    $("#UnitType").select2({
        placeholder: "Select unit type",
        allowClear: true
    });
    $("#FreightClass").select2({
        placeholder: "Select freight class",
        allowClear: true
    });
}
custom__inputDropdown()


function sidedrawer () {
    const openBtn = document.getElementById('openDrawerBtn');
    const closeBtn = document.getElementById('closeDrawerBtn');
    const drawer = document.getElementById('sideDrawer');
    const overlay = document.getElementById('drawerOverlay');

    function openDrawer() {
        drawer.classList.add('active');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeDrawer() {
        drawer.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    openBtn.addEventListener('click', openDrawer);
    closeBtn.addEventListener('click', closeDrawer);
    overlay.addEventListener('click', closeDrawer);

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && drawer.classList.contains('active')) {
            closeDrawer();
        }
    });
}
sidedrawer()


document.querySelectorAll('.menu').forEach(btn => {
    btn.addEventListener('click', e => {
        btn.classList.toggle('active');
    });
});


const menuToggle = document.querySelector('.ark__menu-toggle');
const navLinks = document.querySelector('.ark__nav-links');

menuToggle.addEventListener('click', () => {
  navLinks.classList.toggle('active');
});

// Search box expand functionality for mobile
// function searchBoxExpand() {
//     const searchBox = document.querySelector('.ark__search-box');
//     const searchInput = searchBox.querySelector('input');

//     // Function to check if we're on mobile
//     function isMobile() {
//         return window.innerWidth <= 768;
//     }

//     // Toggle search box on click
//     searchBox.addEventListener('click', function(e) {
//         if (isMobile()) {
//             e.stopPropagation();
//             searchBox.classList.toggle('expanded');
//             // Focus on input when expanded
//             if (searchBox.classList.contains('expanded')) {
//                 setTimeout(() => {
//                     searchInput.focus();
//                 }, 100);
//             }
//         }
//     });

//     // Close search box when clicking outside (mobile only)
//     document.addEventListener('click', function(e) {
//         if (isMobile() && !searchBox.contains(e.target)) {
//             searchBox.classList.remove('expanded');
//         }
//     });

//     // Close search box on window resize if not mobile
//     window.addEventListener('resize', function() {
//         if (!isMobile()) {
//             searchBox.classList.remove('expanded');
//         }
//     });
// }
// searchBoxExpand();