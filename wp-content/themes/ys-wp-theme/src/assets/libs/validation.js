export default class Validate {
    lang = {
        required           : 'Заполните поле',
        minLength          : 'Минимальное количество символов %s',
        maxLength          : 'Максимальное количество символов %s',
        validEmail         : 'Поле должно содержать корректный адрес электронной почты',
        alpha              : 'Поле должно содержать только буквы латинского алфавита',
        alphaCyrLat        : 'Поле должно содержать только буквы латинского и кириллического алфавита',
        alphaCyrLatNumber  : 'Поле должно содержать только буквы латинского, кириллического алфавита и цифры',
        alphaCyrillic      : 'Поле должно содержать только буквы кириллического алфавита',
        alphaCyrillicNumber: 'Поле должно содержать только буквы кириллического алфавита и цифры',
        alphaNumeric       : 'Поле должно содержать только буквы латинского алфавита и цифры',
        alphaDash          : 'Поле должно содержать только буквы латинского алфавита, цифры, подчеркивания и тире',
        numeric            : 'Поле должно содержать цифры',
        integer            : 'Поле должно содержать целые числа',
        decimal            : 'Поле должно содержать десятичное число',
        validUrl           : 'Поле должно содержать ссылки',
        phoneNumber        : 'Введите корректный номер телефона',
        fileType           : 'Вы можете прикреплять только следующие типы файлов %s',
        fileSize           : 'Размер изображения должен быть не более %s',
        alphaCyrLatExtended: 'Поле должно содержать только буквы латинского и кириллического алфавита. Может содержать дефис или тире. Не может содержать слово "точка", а также оканчиваться на пробел'
    };
    regexp = {
        numericRegex            : /^[0-9]+$/,
        integerRegex            : /^\-?[0-9]+$/,
        decimalRegex            : /^\-?[0-9]*\.?[0-9]+$/,
        emailRegex              : /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
        alphaRegex              : /^[a-z]+$/i,
        alphaNumericRegex       : /^[a-z0-9]+$/i,
        alphaDashRegex          : /^[a-z0-9_\-]+$/i,
        alphaCyrillicRegex      : /^[а-яА-ЯёЁ]+$/i,
        alphaCyrillicNumberRegex: /^[а-яА-ЯёЁ0-9]+$/i,
        alphaCyrLatRegex        : /^[а-яА-ЯёЁa-zA-Z]+$/i,
        alphaCyrLatNumberRegex  : /^[а-яА-ЯёЁa-zA-Z0-9\-.,;?!()\s]+$/i,
        urlRegex                : /^((http|https):\/\/(\w+:{0,1}\w*@)?(\S+)|)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?$/,
        phoneNumberRegex        : /^[0-9\-+()\s]+$/,
        alphaCyrLatExtendedRegex: /^(?!.*точка)[а-яА-ЯёЁa-zA-Z-—]+([\s]{1})+[а-яА-ЯёЁa-zA-Z-—]+$|^(?!.*точка)[а-яА-ЯёЁa-zA-Z-—]+$/i,
    };

    constructor(formSelector, fields, customFields, callback) {
        if (!formSelector) {
            throw new Error("Укажите селектор формы");
        }

        this.form = document.querySelector(formSelector);
        this.messages = {};
        this.fields = {};
        this.callback = callback;
        this.customFields = {};
        this.handlers = {};

        for (let i = 0; i < fields.length; i++) {
            let field = fields[i];

            if (!field.name) {
                throw new Error("Не указано поле name");
            }

            if (field.name) {
                this.addField(field, i);
            }
        }

        if (customFields) {
            for (let i = 0; i < customFields.length; i++) {
                let custom = customFields[i];
                this.addCustomFields(custom, i);
            }
        }

        this.form.addEventListener('input', (e) => {
            let element = e.target;

            for (let key in this.fields) {
                if (this.fields.hasOwnProperty(key) && element.getAttribute('name') === this.fields[key].name) {
                    this.removeErrors(element);
                    this.checkFormFly(element);
                }
            }
        });

        this.form.addEventListener('click', (e) => {
            let element = e.target;
            this.checkCustomFieldsFly(element);
        });

        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.removeErrors();
            this.checkForm();
            this.checkCustomField();
            this.ajaxResponse();
        });
    }

    addField(field, idx) {
        this.fields[idx] = {
            name          : field.name,
            minLength     : field.minLength ?? null,
            maxLength     : field.maxLength ?? null,
            rules         : field.rules ?? null,
            customMessage : field.customMessage ?? null,
            customRequired: field.customRequired ?? false,
            positionError : field.positionError ?? null,
            parentElement : field.parentElement ?? null,
            typeError     : field.typeError ?? null,
            numberChar    : field.numberChar ?? null,
            fileTypes     : field.fileTypes ?? null,
            fileSize      : field.fileSize ?? null,
        };
    };

    addCustomFields(customField, idx) {
        this.customFields[idx] = {
            parentElement : customField.parentElement ?? null,
            element       : customField.element ?? null,
            dataAttribute : customField.dataAttribute ?? null,
            customMessage : customField.customMessage ?? null,
            customRequired: customField.customRequired ?? false,
            positionError : customField.positionError ?? null,
            typeError     : customField.typeError ?? null,
            rule          : customField.rule ?? null,
        };
    };

    setupFieldValue(field, newValue, idx = null) {
        if (idx) {
            this.fields[idx][field] = newValue;
            return;
        }

        for (let i in this.fields) {
            if (this.fields.hasOwnProperty(i)) {
                this.fields[i][field] = newValue;
            }
        }
    }

    checkFields(field, formField) {
        let rules = field.rules;
        this.handlers = this.hooks;

        for (let i = 0; i < rules.length; i++) {
            let failed        = false,
                formFieldName = this.form.querySelector('[name="' + formField + '"]');

            if (!formFieldName.value && !formFieldName.checked) {
                failed = true;
            }

            if (typeof this.handlers[rules[i]] === 'function') {
                if (rules[i] === 'minLength' && !this.handlers.minLength.apply(this, [formFieldName, field.minLength])) {
                    failed = true;
                }

                if (rules[i] === 'maxLength' && !this.handlers.maxLength.apply(this, [formFieldName, field.maxLength])) {
                    failed = true;
                }

                if (rules[i] !== 'minLength' && rules[i] !== 'maxLength' && !this.handlers[rules[i]].apply(this, [formFieldName, field])) {
                    failed = true;
                }
            }

            failed && this.errorMessage(field, rules[i], formFieldName);
        }
    };

    checkCustomField() {
        if (!this.customFields) {
            return;
        }
        let parentElement;

        for (let key in this.customFields) {
            if (!this.customFields.hasOwnProperty(key)) {
                continue;
            }

            if (this.customFields[key].parentElement) {
                parentElement = this.form.querySelector(this.customFields[key].parentElement);
            }

            let customField = this.form.querySelectorAll(this.customFields[key].element),
                data        = this.customFields[key].dataAttribute,
                failed      = false;

            for (let i = 0; i < customField.length; i++) {
                if (!customField[i].getAttribute(data) || customField[i].getAttribute(data) === 0) {
                    failed = true;
                }
                if (failed && this.customFields[key].parentElement) {
                    this.insertError(this.createErrors(
                        this.customFields[key].customMessage,
                        this.customFields[key].rule, parentElement,
                        this.customFields[key].typeError),
                        parentElement,
                        this.customFields[key].positionError,
                    );
                    return;
                }
                if (failed) {
                    this.insertError(this.createErrors(
                        this.customFields[key].customMessage,
                        this.customFields[key].rule, customField[i],
                        this.customFields[key].typeError),
                        customField[i],
                        this.customFields[key].positionError
                    );
                }
            }
        }
    };

    errorMessage(field, rules, formField) {
        let parentElement = this.form.querySelector(field.parentElement);
        this.messages = this.lang;
        for (let key in this.messages) {
            if (!this.messages.hasOwnProperty(key)) {
                continue;
            }

            if (key === 'required' && rules === 'required'
                && field.parentElement) {
                this.insertError(this.createErrors(
                    this.customError(this.messages[key], field), key, parentElement, field.typeError),
                    parentElement,
                    field.positionError,
                );
            }

            if (key === rules && key !== 'minLength'
                && rules !== 'minLength' && key !== 'maxLength'
                && rules !== 'maxLength' && key !== 'required'
                && rules !== 'required' && key !== 'fileType'
                && rules !== 'fileType' && key !== 'fileSize'
                && rules !== 'fileSize' && formField.value.length > 0) {
                this.insertError(this.createErrors(
                    this.customError(this.messages[key], field), key, formField, field.typeError),
                    formField,
                    field.positionError,
                );
            }

            if (key === 'required' && rules === 'required'
                && !field.customRequired) {
                this.insertError(this.createErrors(
                    this.messages[key], key, formField, field.typeError),
                    formField,
                    field.positionError
                );
            }

            if (key === 'required' && rules === 'required'
                && !formField.checked && !field.parentElement
                && field.customRequired) {
                this.insertError(this.createErrors(
                    this.customError(this.messages[key], field), key, formField, field.typeError),
                    formField,
                    field.positionError,
                );
            }

            if (key === 'maxLength' && rules === 'maxLength' && formField.value.length > field.maxLength) {
                this.insertError(this.createErrors(
                    this.messages[key].split('%s').join(field.maxLength), key, formField, field.typeError),
                    formField,
                    field.positionError
                );
            }

            if (key === 'minLength' && rules === 'minLength' && formField.value.length > 0) {
                this.insertError(this.createErrors(
                    this.messages[key].split('%s').join(field.minLength), key, formField, field.typeError),
                    formField,
                    field.positionError
                );
            }

            if (key === 'fileType' && rules === 'fileType' && formField.files.length > 0) {
                this.insertError(this.createErrors(
                    this.messages[key].split('%s').join(field.fileTypes).replace(',', ', '), key, formField, field.typeError),
                    formField,
                    field.positionError
                );
            }

            if (key === 'fileSize' && rules === 'fileSize' && formField.files.length > 0) {
                this.insertError(this.createErrors(
                    this.messages[key].split('%s').join(this.convertBytes(field.fileSize)), key, formField, field.typeError),
                    formField,
                    field.positionError
                );
            }
        }
        return this;
    };

    createErrors(text, rule, formField, typeError) {
        let error     = document.createElement('div'),
            errorText = document.createElement('div'),
            parent    = formField.closest('.error-inner') || formField.closest('label');

        if (rule === 'required') {
            error.className = 'error-message error-primary';
            parent && parent.classList.add('is-error');
        } else {
            error.className = 'error-message error-extra';
            parent && parent.classList.add('is-passed');
        }

        errorText.className = 'error-text';
        errorText.innerHTML = text;

        if (typeError === 'withArrow') {
            let errorArrow = document.createElement('div');
            errorArrow.className = 'error-arrow';
            error.append(errorArrow);
        }

        error.append(errorText);
        return error;
    };

    removeErrors(element) {
        let parent;

        if (element) {
            parent = element.closest('.error-inner') || element.closest('label');
            if (parent) {
                parent.classList.remove('is-error');
                parent.classList.remove('is-passed');
            }
        } else {
            let errorClasses = this.form.querySelectorAll('.is-error, .is-passed');
            errorClasses.classList.remove('is-error');
            errorClasses.classList.remove('is-passed');
        }

        let selector = parent ? parent : this.form,
            errors   = selector.querySelectorAll('.error-message');

        for (let i = 0; i < errors.length; i++) {
            errors[i].remove();
        }
    };

    insertError(element, insertElement, position) {
        let positions = [
            'afterend',
            'beforeend',
            'afterbegin',
            'beforebegin',
        ];

        if (positions.includes(position)) {
            //insertElement.parentNode.insertAdjacentHTML(position, element.outerHTML);
        }

        if (positions.includes(position)) {
            insertElement.insertAdjacentHTML(position, element.outerHTML);
        }
    };

    customError(error, field) {
        if (field.customMessage) {
            return field.customMessage;
        }

        return error;
    };

    checkForm() {
        let elements     = this.form.querySelectorAll('input, textarea, select'),
            elementsData = [],
            fieldsData   = [];

        for (let i = 0; i < elements.length; i++) {
            elements[i].name && elementsData.push(elements[i].name);
        }

        for (let key in this.fields) {
            if (this.fields.hasOwnProperty(key)) {
                fieldsData.push(this.fields[key].name);
            }
        }

        let mergeArray = elementsData.filter(function (obj) {
            return fieldsData.indexOf(obj) >= 0;
        });

        for (let i = 0; i < mergeArray.length; i++) {
            if (mergeArray[i] === this.fields[i].name) {
                this.checkFields(this.fields[i], mergeArray[i]);
            }
        }
    };

    checkFormFly(element) {
        for (let key in this.fields) {
            if (this.fields.hasOwnProperty(key) && element.name === this.fields[key].name) {
                this.checkFields(this.fields[key], element.name);
            }
        }
    };

    checkCustomFieldsFly(element) {
        for (let key in this.customFields) {
            if (this.customFields.hasOwnProperty(key) && element.getAttribute(this.customFields[key].dataAttribute)) {
                this.removeErrors(element);
                this.checkCustomField(element);
            }
        }
    }

    ajaxResponse() {
        let errors = this.form.querySelectorAll('.error-message'),
            failed = false;

        for (let i = 0; i < errors.length; i++) {
            errors && (failed = true);
        }
        if (!failed && typeof this.callback === 'function') {
            this.callback(this);
        }
    };

    convertBytes(bytes, decimals = 2) {
        if (bytes === 0) {
            return;
        }

        const number = 1024,
              digits = decimals < 0 ? 0 : decimals,
              sizes  = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
              result = Math.floor(Math.log(bytes) / Math.log(number));

        return parseFloat((bytes / Math.pow(number, result)).toFixed(digits)) + ' ' + sizes[result];
    }

    stopTyping(field) {
        field.value = field.value.substring(0, field.value.length - 1);
        return field.value;
    }

    hooks = {
        numeric            : (field, characters) => {
            if (characters.numberChar) {
                if (!this.regexp.numericRegex.test(field.value)
                    || field.value.length > characters.numberChar) {
                    this.stopTyping(field);
                }
                if (!this.regexp.numericRegex.test(field.value)
                    || field.value.length < characters.numberChar) {
                    return false;
                }
            } else {
                if (!this.regexp.numericRegex.test(field.value)) {
                    return false;
                }
            }
            return true;
        },
        alphaCyrLatNumber  : field => this.regexp.alphaCyrLatNumberRegex.test(field.value.replace(/\s\s+/g, '')),
        alphaCyrillic      : field => this.regexp.alphaCyrillicRegex.test(field.value),
        alphaCyrLatExtended: field => this.regexp.alphaCyrLatExtendedRegex.test(field.value),
        validEmail         : field => this.regexp.emailRegex.test(field.value),
        alpha              : field => this.regexp.alphaRegex.test(field.value),
        alphaNumeric       : field => this.regexp.alphaNumericRegex.test(field.value),
        alphaCyrLat        : field => this.regexp.alphaCyrLatRegex.test(field.value),
        alphaCyrillicNumber: field => this.regexp.alphaCyrillicNumberRegex.test(field.value),
        alphaDash          : field => this.regexp.alphaDashRegex.test(field.value),
        integer            : field => this.regexp.integerRegex.test(field.value),
        decimal            : field => this.regexp.decimalRegex.test(field.value),
        validUrl           : field => this.regexp.urlRegex.test(field.value),
        phoneNumber        : (field, characters) => {
            const number = field.value.replace(/\D/g, '');
            if (characters.numberChar) {
                if (!this.regexp.phoneNumberRegex.test(field.value) ||
                    number.length > characters.numberChar) {
                    this.stopTyping(field);
                }
                if (!this.regexp.phoneNumberRegex.test(field.value)
                    || number.length < characters.numberChar) {
                    return false;
                }
            } else {
                if (!this.regexp.phoneNumberRegex.test(field.value)) {
                    return false;
                }
            }
            return true;
        },
        fileType           : (field, types) => {
            let mimeTypes = types.fileTypes,
                type      = [];

            if (field.files[0]) {
                type = field.files[0].type.split('/')[1];
            }
            return mimeTypes.indexOf(type) !== -1;
        },
        fileSize           : (field, size) => {
            let maxFileSize  = size.fileSize,
                loadFileSize = '';

            field.files[0] && (loadFileSize = field.files[0].size);
            return loadFileSize <= maxFileSize;
        },
        minLength          : (field, length) => {
            if (!this.regexp.numericRegex.test(length)) {
                return false;
            }
            return (field.value.length >= parseInt(length, 10));
        },
        maxLength          : (field, length) => {
            if (!this.regexp.numericRegex.test(length)) {
                return false;
            }
            return (field.value.length <= parseInt(length, 10));
        },
    };
}