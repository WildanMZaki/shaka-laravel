class WzValidator {
    constructor({
        ruleSeparator = ',',
        keyValueSeparator = ':',
        messages = {},
        clear = null,
        error = null,
    } = {}){
        this.separator = ruleSeparator;
        this.keyValueSeparator = keyValueSeparator;
        this.rules = {};
        this.listRules = [];
        this.clear = clear;
        this.error = error;
        this.defaultMessages = {
            required: '{name} tidak boleh kosong',
            number: '{name} harus angka',
        }
    }

    extractRuleOf(element) {
        const rawRules = $(element).data('wzVrules');
        const listRules = rawRules.split(this.separator);
        this.listRules = listRules;
        listRules.forEach(rule => {
            const parts = rule.split(this.keyValueSeparator);
            this.rules[parts[0]] = parts.length == 1 ? true : parts[1];
        });
    }

    getMessage(element) {
        let alias = $(element).data('alias');
        if (!alias) {
            alias = $(element).attr('name');
            if (!alias) {
                alias = $(element).attr('id');
            }
        }
        let message = this.defaultMessages[this.rule];
        message = message.replace('{name}', alias);
        message = message.replace('{val}', this.ruleValue);
        return message;
    }

    validate(element){
        this.extractRuleOf(element);
        this.value = $(element).val();

        for (const rule in this.rules) {
            if (Object.hasOwnProperty.call(this.rules, rule)) {
                const ruleValue = this.rules[rule];
                this.rule = rule;
                this.ruleValue = ruleValue;
                const valid = this[rule]();
                if (!valid) {
                    const msg = this.getMessage(element);
                    if (this.error != null) {
                        this.error(msg, element);
                    } else {
                        alert(msg);
                    }
                    break;
                } else {
                    if (this.clear != null) {
                        this.clear(element);
                    }
                }
            }
        }

    }

    required() {
        const invalidValues = [undefined, null, ""];
        let status;
        if (this.ruleValue) {
          if (this.listRules.includes('number')) {
            invalidValues.push(0);
            return !invalidValues.includes(Number(this.value));
          }
          return !invalidValues.includes(this.value);
        }
        return true;
    }

    number() {
        const trueVal = this.value.replace('.', '');
        if (isNaN(Number(trueVal))) {
            return false;
        }
        return true;
    }
}
