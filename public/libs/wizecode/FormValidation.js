// Ketentuan Class ini:
/**
 * 1. Harus Menggunakan jQuery & Bootstrap
 * 2. Setiap Input Form, jika ingin menggunakan aksi static, harus memiliki sibling yang punya class invalid-feedback
 *      Jika tidak, maka akan berikan alert()
 * 3. Setiap element dikenali oleh rule yang mengunakan name attribut sebagai selector
 * 4. Ada aksi static: tambah class is-invalid pada form, dan berikan message custom/default. Jika ingin custom error action, berikan callback function. Pada rulenya juga
 * 5. ada fungsi validate yang akan return true/false
 */

// Format rule seperti Laravel:
/**
 * $request->validate([
            'username' => ['required', 'unique:users,username', 'max:255'],
            'phone' => ['required'],
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'username.required' => 'Username harus diisi',
            'username.unique' => 'Username tersebut telah digunakan',
            'username.max' => 'Username tidak boleh lebih dari :max karakter',
            'phone.required' => 'Nomor ponsel harus diisi',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password setidaknya harus :min karakter',
            'password.confirmed' => 'Konfirmasi password tidak sesuai',
        ]);
 */

class FormValidation {
  constructor(
    formSelector,
    {
      allRequired = true,
      optionals = [],
      orders = ["required", "min", "max", "allCharNumber", "matchWith"],
      fields = {},
    } = {},
    messages = {}
  ) {
    if (!formSelector) {
      throw Error("Form selector diperlukan");
    }

    this.form = $(formSelector);
    this.rules = {
      allRequired,
      optionals,
      fields,
    };
    this.messages = messages;
    this.inputs = [];
    this.currentInput = null;
    this.currentRule = null;
    this.currentRuleValue = null;

    this.mapValidation = {};
    this.conclutionOfFields = {};

    this.rulesOrder = orders;

    this.minDefault = 1;
    this.maxDefault = 255;

    $(`${formSelector} input`).each((i, input) => {
      const inputName = $(input).attr("name");
      var rules = this.rules.fields.hasOwnProperty(inputName)
        ? this.rules.fields[inputName]
        : {};
      if (!rules.hasOwnProperty("required"))
        rules = { required: true, ...rules };

      const isHidden = $(input).attr("type") == "hidden";
      if (!isHidden) {
        this.inputs.push({
          name: inputName,
          alias: $(input).data("alias") ?? this.alias(inputName),
          rules: rules,
          element: $(input),
        });
        this.mapValidation[inputName] = {};
        this.conclutionOfFields[inputName] = true;
      }
    });
  }

  alias = (inputName) => {
    let alias;
    if (inputName && typeof inputName === "string") {
      alias = inputName.charAt(0).toUpperCase() + inputName.slice(1);
      alias = alias.replace(/_/g, " ");
    } else {
      alias = inputName;
    }
    return alias ?? "";
  };

  isRequired = (input) => {
    const inputName = $(input).attr("name");

    if (this.rules.allRequired && !this.rules.optionals.includes(inputName)) {
      return true;
    }

    return false;
  };

  alert = () => {
    const next = this.currentInput.element.next();
    const message = this.getErrorMessage();
    this.currentInput.element.addClass("is-invalid");
    if (next.hasClass("invalid-feedback")) {
      next.html(message);
    } else {
      if (this.rules.fields[this.currentInput.name].hasOwnProperty("action")) {
        this.rules.fields[this.currentInput.name].action(message);
        return;
      }
      alert(message);
    }
  };

  clear = () => {
    const next = this.currentInput.element.next();
    this.currentInput.element.removeClass("is-invalid");
    if (next.hasClass("invalid-feedback")) {
      next.html("");
    } else {
      if (
        this.rules.fields[this.currentInput.name].hasOwnProperty("clearAction")
      ) {
        this.rules.fields[this.currentInput.name].clearAction();
        return;
      }
    }
  };

  validate = () => {
    this.validated = true;
    this.inputs.forEach((input) => {
      this.currentInput = input;
      Object.entries(input.rules).forEach(([key, value]) => {
        this.currentRule = key;
        this.currentRuleValue = value;
        if (key == "action" || key == "clearAction") {
          return;
        }

        this.mapValidation[this.currentInput.name][key] = this.isValid();
        // const validation = this.isValid();

        // console.log(
        //   `Now validate ${this.currentInput.alias} in rule ${key} with value ${value}`
        // );
        // console.log(validation);

        // if (!validation) {
        //   this.mapValidation[this.currentInput.name][key] = false;
        // } else {
        //   this.mapValidation[this.currentInput.name][key] = true;
        // }
      });
      // Sekarang di sini hitung setiap rule dari satu field, jika masih ada yang false. Maka conclution = false;
      const conclutionOfThisField = Object.values(
        this.mapValidation[this.currentInput.name]
      ).every((value) => value === true);
      if (!conclutionOfThisField) {
        this.alert();
      } else {
        this.clear();
      }
      this.conclutionOfFields[this.currentInput.name] = conclutionOfThisField;
    });
    const lastConclution = Object.values(this.conclutionOfFields).every(
      (value) => value === true
    );
    return lastConclution;
  };

  isValid = () => {
    const value = this.currentInput.element.val();
    const rules = {
      required: () => {
        const invalidValues = [undefined, null, ""];
        if (this.currentRuleValue) {
          return !invalidValues.includes(value);
        }
        return true;
      },
      min: () => {
        return value.length >= this.currentRuleValue;
      },
      max: () => {
        return value.length < this.currentRuleValue;
      },
      allCharNumber: () => {
        return /^\d+$/.test(value);
      },
      matchWith: () => {
        const valueMatch = $(`input[name="${this.currentRuleValue}"]`).val();
        return value == valueMatch;
      },
    };
    return rules[this.currentRule]();
  };

  getErrorMessage = () => {
    const errors = {
      required: `${this.currentInput.alias} harus diisi`,
      min: `${this.currentInput.alias} setidaknya harus ${
        this.currentInput.rules.hasOwnProperty("min")
          ? this.currentInput.rules.min
          : this.minDefault
      } karakter`,
      max: `${this.currentInput.alias} terlalu panjang. Maksimal: ${
        this.currentInput.rules.hasOwnProperty("max")
          ? this.currentInput.rules.max
          : this.maxDefault
      } karakter`,
      allCharNumber: `${this.currentInput.alias} tidak valid (harus angka)`,
      matchWith: `${this.currentInput.alias} tidak sesuai`,
    };
    // return errors[this.currentRule];

    for (const rule of this.rulesOrder) {
      if (this.mapValidation[this.currentInput.name].hasOwnProperty(rule)) {
        if (!this.mapValidation[this.currentInput.name][rule]) {
          return errors[rule];
        }
      }
    }
  };

  mustDigit = (element) => {
    element.value = element.value.replace(/[^0-9]/g, "");
  };
}
