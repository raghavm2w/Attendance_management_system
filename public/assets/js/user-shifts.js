
document.addEventListener('DOMContentLoaded', function () {
    const assignShiftForm = document.getElementById('assignShiftForm');

    if (assignShiftForm) {
        assignShiftForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            // Clear previous errors
            clearFormErrors(this);

            try {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

                const formData = new FormData(this);
                const data = Object.fromEntries(formData.entries());

                const response = await fetch('/admin/assign-shift', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.status === 'success') {
                    if (result.data?.csrf) {
                        document.querySelector('meta[name="csrf-token"]').setAttribute('content', result.data.csrf);
                    }
                    showAlert(result.message, 'success');
                    assignShiftForm.reset();
                } else {
                    console.log(result.errors.csrf);
                    document.querySelector('meta[name="csrf-token"]').setAttribute('content', result.errors.csrf);
                    if (result.errors.errors) {

                        const errors = result.errors.errors;

                        if (typeof errors === 'object') {
                            Object.entries(errors).forEach(([field, message]) => {
                                const input = assignShiftForm.querySelector(`[name="${field}"]`);
                                if (input) {
                                    showError(input, message);
                                }
                            });
                        } else {
                            showAlert(result.message, 'error');
                        }

                    } else {
                        showAlert(result.message || 'An error occurred', 'error');
                    }
                }

            } catch (error) {
                console.error('Error:', error);
                showAlert('An unexpected error occurred', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    }

    function showError(input, message) {
        input.classList.add('is-invalid');
        let feedback = input.nextElementSibling;
        if (!feedback || !feedback.classList.contains('invalid-feedback')) {
            // If input is in input-group, we might need to look at parent's sibling or append to parent
            const parent = input.closest('.input-group');
            if (parent) {
                feedback = parent.nextElementSibling;
                if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                    feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    parent.parentNode.insertBefore(feedback, parent.nextSibling);
                }
            } else {
                feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                input.parentNode.insertBefore(feedback, input.nextSibling);
            }
        }
        feedback.textContent = message;
        feedback.style.display = 'block';
    }

    function clearFormErrors(form) {
        form.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });

        form.querySelectorAll('.invalid-feedback').forEach(el => {
            el.style.display = 'none';
            el.textContent = '';
        });

        // Also remove any general alerts if we decided to put them in the form
    }


});
