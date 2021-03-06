$(function () {
    var addQuestionBtnSelector = ".add-question-btn";
    var addVariantBtnSelector = ".add-variant-btn";

    var questionsList = $(".questions-list");
    questionsList.data("index", questionsList.find(".question-item").length);

    questionsList.on("click", addQuestionBtnSelector, function (e) {
        e.preventDefault();
        addQuestion(questionsList, $(addQuestionBtnSelector));
    });

    questionsList.on("click", addVariantBtnSelector, function (e) {
        e.preventDefault();
        var currentList = $(this).parents(".variants-list");
        addVariant(currentList, $(this));
    });

    questionsList.on('click', '.remove-variant-btn', function (e) {
        e.preventDefault();
        $(this).parents('.variant-container').remove();
    });

    $(".add-tag-btn").on('click', function (e) {
        e.preventDefault();
        addTag($('.tags-list'), $(this));
    });

    $(".tags-list").on('click', '.remove-tag-btn', function(e) {
        e.preventDefault();
        $(this).parents('li.tag-item').remove();
    });

    function addQuestion(list, button) {
        var prototype = list.data("prototype-question");
        var index = list.data('index');

        var newForm = prototype.replace(/__question_number__/g, index);
        list.data('index', index + 1);

        var newFormItem = $('<li class="question-item" ' +
            'data-serial-number="'+index+'"></li>').append(newForm);
        button.before(newFormItem);

        var hiddenSerialNumber = $(newFormItem).find('input[type=hidden]');
        hiddenSerialNumber.val(index + 1);

        var variantsContainer = $("#variants-template").html();
        newFormItem.append(variantsContainer);

        var variantsList = $(newFormItem).find(".variants-list");
        var currentButton = variantsList.find(addVariantBtnSelector);

        addVariant(variantsList, currentButton);
    }

    function addVariant(list, button) {
        var prototype = questionsList.data("prototype-variant");
        var index = list.find(".variant-item").length;

        var newForm = prototype.replace(/__variant_number__/g, index);
        var questionNumber = list.parents(".question-item").data("serial-number");
        newForm = newForm.replace(/__question_number__/g, questionNumber);
        var variantContainer = $($('#single-variant-template').html());
        var li = variantContainer.find('.variant-item');
        li.append(newForm);

        button.before(variantContainer);
    }

    function addTag(list, button) {
        var prototype = list.data("prototype-tag");
        var index = list.find(".tag-item").length;

        var newForm = prototype.replace(/__tag_number__/g, index);
        var tagsContainer = $($('#tags-template').html());
        var inputWrapper = tagsContainer.find(".input-wrapper");
        inputWrapper.append(newForm);

        button.before(tagsContainer);
    }

});
