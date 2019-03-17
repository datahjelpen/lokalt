const moment = require("moment");
const Pikaday = require("pikaday");
const calendarI18N = {
    previousMonth: "<",
    nextMonth: ">",
    months: [
        "Januar",
        "Februar",
        "Mars",
        "April",
        "Mai",
        "Juni",
        "Juli",
        "August",
        "September",
        "Oktober",
        "November",
        "Desember"
    ],
    weekdays: [
        "Søndag",
        "Mandag",
        "Tirsdag",
        "Onsdag",
        "Torsdag",
        "Fredag",
        "Lørdag"
    ],
    weekdaysShort: ["Søn", "Man", "Tir", "Ons", "Tor", "Fre", "Lør"]
};

(function() {
    const placeForms = document.querySelectorAll("form.form-place");
    placeForms.forEach(placeForm => {
        const btnsAddNew = placeForm.querySelectorAll(
            ".form-place-special-hours-add"
        );

        btnsAddNew.forEach(btn => {
            btn.addEventListener("click", placeFormSpecialHoursAdd);
        });

        // Add special hours so we can edit them
        let xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                const openingHours = JSON.parse(this.responseText);

                openingHours.special.forEach(special => {
                    btnsAddNew[0].click();
                    const specialHourFields = placeForm.querySelectorAll(
                        "fieldset.form-group-opening-hours-special"
                    );
                    const specialHourField =
                        specialHourFields[specialHourFields.length - 1];
                    const dateField = specialHourField.querySelector(
                        ".input-opening-hours-special-date"
                    );
                    const toggleField = specialHourField.querySelector(
                        ".input-toggle"
                    );
                    const fromField = specialHourField.querySelector(
                        ".input-opening-hours-special-from"
                    );
                    const toField = specialHourField.querySelector(
                        ".input-opening-hours-special-to"
                    );
                    const infoField = specialHourField.querySelector(
                        ".open_hours_from-special-info"
                    );
                    const idField = specialHourField.querySelector(
                        ".open_hours_from-special-id"
                    );

                    dateField.value = special.special_hours_date;
                    infoField.value = "edit";
                    idField.value = special.id;

                    if (special.time_from != null) {
                        toggleField.checked = true;
                        dateField.value = special.special_hours_date;
                        fromField.value = special.time_from;
                        toField.value = special.time_to;
                    }
                });
            }
        };

        const placeId = placeForm.querySelector(".form-place-id").value;
        xhr.open("GET", "/steder/" + placeId + "/apningstider", true);
        xhr.send();
    });

    function placeFormSpecialHoursAdd(event) {
        event.preventDefault();

        const specialHoursWrapper = this.parentNode.parentNode.querySelector(
            ".form-place-special-hours-wrapper"
        );
        const randomStr = Math.random()
            .toString(36)
            .substr(2, 10);

        // FIELDSET
        let newFieldset = document.createElement("fieldset");
        newFieldset.classList =
            "form-group form-group-opening-hours form-group-opening-hours-special";

        // LEGEND START
        let newLegend = document.createElement("legend");
        newLegend.appendChild(document.createTextNode("Dato: "));

        let newInputDate = document.createElement("input");
        newInputDate.setAttribute(
            "name",
            "open_hours_from_special_dates[" + randomStr + "]"
        );
        newInputDate.setAttribute("placeholder", "YYYY-MM-DD");
        newInputDate.setAttribute("type", "text");
        newInputDate.setAttribute("required", "");
        newInputDate.classList = "input-opening-hours-special-date";
        setupSpecialHourDateField(newInputDate);
        newLegend.appendChild(newInputDate);
        // LEGEND END

        // CHECKBOX START
        let newInputCheckbox = document.createElement("input");
        newInputCheckbox.setAttribute("type", "checkbox");
        newInputCheckbox.setAttribute(
            "id",
            "form-place-" + randomStr + "-open_closed"
        );
        newInputCheckbox.classList = "input-toggle";

        let newInputCheckboxLabel = document.createElement("label");
        newInputCheckboxLabel.setAttribute(
            "for",
            "form-place-" + randomStr + "-open_closed"
        );
        newInputCheckboxLabel.classList = "input-toggle-label";

        let newInputCheckboxLabelSpan = document.createElement("span");
        newInputCheckboxLabelSpan.classList = "input-toggle-off";
        newInputCheckboxLabelSpan.appendChild(
            document.createTextNode("Stengt")
        );
        newInputCheckboxLabel.appendChild(newInputCheckboxLabelSpan);

        newInputCheckboxLabelSpan = document.createElement("span");
        newInputCheckboxLabelSpan.classList = "input-toggle-on";
        newInputCheckboxLabelSpan.appendChild(document.createTextNode("Åpent"));
        newInputCheckboxLabel.appendChild(newInputCheckboxLabelSpan);
        // CHECKBOX END

        // INPUT TIME START
        let newInputHoursWrapper = document.createElement("div");
        newInputHoursWrapper.classList = "input-toggle-target";

        let newInputHoursWrapperInner = document.createElement("div");
        newInputHoursWrapperInner.classList = "input-toggle-on";
        newInputHoursWrapper.appendChild(newInputHoursWrapperInner);

        // open from
        let newInputOpenFrom = document.createElement("input");
        newInputOpenFrom.setAttribute(
            "name",
            "open_hours_from_special_opens[" + randomStr + "]"
        );
        newInputOpenFrom.setAttribute("type", "text");
        newInputOpenFrom.setAttribute("placeholder", "Åpner kl.");
        newInputOpenFrom.setAttribute(
            "pattern",
            "([01]?[0-9]|2[0-3]):[0-5][0-9]"
        );
        newInputOpenFrom.setAttribute("autocomplete", "off");
        newInputOpenFrom.setAttribute("list", "available-hours");
        newInputOpenFrom.classList = "input-opening-hours-special-from";
        newInputOpenFrom.addEventListener("invalid", invalidTimeInput);
        newInputOpenFrom.addEventListener("input", inputTimeInput);
        newInputHoursWrapperInner.appendChild(newInputOpenFrom);

        // span
        let newInputOpenFromToSpan = document.createElement("span");
        newInputOpenFromToSpan.appendChild(document.createTextNode("—"));
        newInputHoursWrapperInner.appendChild(newInputOpenFromToSpan);

        // open to
        let newInputOpenTo = document.createElement("input");
        newInputOpenTo.setAttribute(
            "name",
            "open_hours_from_special_closes[" + randomStr + "]"
        );
        newInputOpenTo.setAttribute("type", "text");
        newInputOpenTo.setAttribute("placeholder", "Stenger kl.");
        newInputOpenTo.setAttribute(
            "pattern",
            "([01]?[0-9]|2[0-3]):[0-5][0-9]"
        );
        newInputOpenTo.setAttribute("autocomplete", "off");
        newInputOpenTo.setAttribute("list", "available-hours");
        newInputOpenTo.classList = "input-opening-hours-special-to";
        newInputOpenTo.addEventListener("invalid", invalidTimeInput);
        newInputOpenTo.addEventListener("input", inputTimeInput);
        newInputHoursWrapperInner.appendChild(newInputOpenTo);
        // INPUT TIME END

        // Hidden input to indicate if the special time is being deleted
        let newInputHiddenInfo = document.createElement("input");
        newInputHiddenInfo.setAttribute(
            "name",
            "open_hours_from_special_info[" + randomStr + "]"
        );
        newInputHiddenInfo.classList = "open_hours_from-special-info";
        newInputHiddenInfo.setAttribute("type", "hidden");
        newInputHiddenInfo.value = "add";

        let newInputHiddenId = document.createElement("input");
        newInputHiddenId.setAttribute(
            "name",
            "open_hours_from_special_id[" + randomStr + "]"
        );
        newInputHiddenId.classList = "open_hours_from-special-id";
        newInputHiddenId.setAttribute("type", "hidden");

        let newBtnDelete = document.createElement("button");
        newBtnDelete.setAttribute("type", "button");
        newBtnDelete.setAttribute("aria-label", "slett");
        newBtnDelete.appendChild(document.createTextNode("X"));
        newBtnDelete.classList = "opening-hours-special-delete";
        newBtnDelete.addEventListener("click", deleteSpecialHoursEntry);

        // Append elements to the fieldset
        newFieldset.appendChild(newLegend);
        newFieldset.appendChild(newInputCheckbox);
        newFieldset.appendChild(newInputCheckboxLabel);
        newFieldset.appendChild(newInputHoursWrapper);
        newFieldset.appendChild(newInputHiddenInfo);
        newFieldset.appendChild(newInputHiddenId);
        newFieldset.appendChild(newBtnDelete);

        // Append fieldset to the form
        specialHoursWrapper.appendChild(newFieldset);
        specialHoursWrapper.classList.add("has-children");
    }

    function invalidTimeInput() {
        this.setCustomValidity("Fyll ut timer på 24 timers formatet (tt:mm).");
    }

    function inputTimeInput() {
        this.setCustomValidity("");
    }

    function setupSpecialHourDateField(dateField) {
        new Pikaday({
            field: dateField,
            i18n: calendarI18N
        });
    }

    function deleteSpecialHoursEntry(event) {
        event.preventDefault();

        const fieldset = this.parentNode;
        fieldset.classList = "hidden";

        // Make sure we don't have any required input fields
        // that would prevent the form from submiting.
        fieldset.querySelectorAll("input").forEach(input => {
            input.removeAttribute("required");
        });

        const hiddenInput = fieldset.querySelector(
            "input.open_hours_from-special-info"
        );
        hiddenInput.value = "delete";
    }
})();
