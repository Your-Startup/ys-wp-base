import ysApi from "../../libs/api";
import Validate from "../../libs/validation";

/**
 * @var YS_SITE_DATA
 */

export function authForm() {
    let form = document.querySelector('.js-auth-form');
    if (!form) {
        return;
    }

    new Validate('.js-auth-form', [
            {
                name         : 'name',
                rules        : ['required'],
                positionError: 'afterend',
            },
            {
                name         : 'password',
                rules        : ['required', 'alphaNumeric'],
                positionError: 'afterend',
            }
        ], [], function () {
            let form      = this.form,
                submitBtn = form.elements['submit'],
                params    = {
                    name    : form.elements['name'].value,
                    password: form.elements['password'].value,
                };

            submitBtn.disabled = true;

            ysApi.post('users/auth', params)
                .then(response => {
                    if (response.success !== true) {
                        submitBtn.disabled = false;
                        this.insertError(this.createErrors(response.message, 'required', submitBtn), submitBtn, 'afterend')
                        return;
                    }

                    document.location.href = YS_SITE_DATA.url + '/user';
                })
                .catch((error) => {
                    submitBtn.disabled = false;
                });
        }
    );
}

export function resetPasswordForm() {
    let form = document.querySelector('.js-reset-password-form');
    if (!form) {
        return;
    }

    //new Validate();
}

export function registrationForm() {
    let form = document.querySelector('.js-registration-form');
    if (!form) {
        return;
    }
    new Validate('.js-registration-form', [
            {
                name         : 'full_name',
                rules        : ['required'],
                positionError: 'afterend',
            },
            {
                name         : 'login',
                rules        : ['required'],
                positionError: 'afterend',
            },
            {
                name         : 'email',
                rules        : ['required', 'emailRegex'],
                positionError: 'afterend',
            },
            {
                name         : 'phone',
                rules        : ['required', 'phoneNumberRegex'],
                positionError: 'afterend',
            }
        ], [], function () {
            let form      = this.form,
                submitBtn = form.elements['submit'],
                params    = {
                    full_name: form.elements['full_name'].value,
                    login    : form.elements['login'].value,
                    email    : form.elements['email'].value,
                    phone    : form.elements['phone'].value,
                };

            submitBtn.disabled = true;

            ysApi.put('users/registration', params)
                .then(response => {
                    if (response.success !== true) {
                        console.log(response.message);

                        submitBtn.disabled = false;
                        this.insertError(this.createErrors(response.message, 'required', submitBtn), submitBtn, 'afterend')
                        return;
                    }

                    console.log(response.message);
                })
                .catch((error) => {
                    submitBtn.disabled = false;
                });
        }
    );
}