var categories;
var textEditor;

var nextID = 0;
var $selectedElement = $("#content");
var dragging = false;
var newImages = {};

var debugSaveEnabled = false;

$(document).ready(function() {

    $("#content").css({
        "height" : ($(window).height() - $("#side-nav").height()) + "px"
    });

    $("#side-nav-button").sideNav();
    $(".collapsible").collapsible();

    category.loadTree();
    categories = category.getCategories();
    var category_image = {};
    /*
    NOT IMPLEMENTED
    for(var cat in categories)
        category_image[cat] = "../category/images/" + cat + ".jpg";
    $("#edit-page-category").autocomplete({
        data: category_image,
        limit: 10,
        minLength: 1
    });
    */

    initDialogs();
});

function initDialogs() {

    // Page Dialog

    $("#edit-page-dialog").modal({
        dismissible: true,
        endingTop: "50%"
    });

    $("#edit-page-name").val(pageName);
    $("#edit-page-category").val(pageCategory);
    $("#edit-page-height").val($("#content").height());

    $("#edit-page-button").click(editPage);

    // Image Dialog

    $("#image-create-dialog").modal({
        dismissible: true,
        endingTop: '50%',
        complete: function() {
            $("#image-create-dialog .input").each(function(){
                $(this).val('').blur().removeClass("valid invalid");
            });
            invalidateImage();
        } 
    });

    $("#image-create-button").click(createImage);

    // Text Dialog

    $("#text-create-dialog").modal({
        dismissible: true,
        endingTop: '50%',
        complete: function() {
            textEditor.content.set('');
            $("#text-create-dialog .collapsible").collapsible('close', 0);
            $("#text-create-border-style").val('none');
            $("#text-create-border-color").val('#000000');
            $("#text-create-border-width").val('0');
            
        }
    });

    $("#text-create-border-style").material_select();
    $("#text-create-border-color").colorpicker();

    textEditor = textboxio.replace("#text-create-content", {
        autosubmit: false,
        css : {
            stylesheets: [''],
            styles: [               
                { rule: 'p',    text: 'Párrafo' },
                { rule: 'h1',   text: 'Encabezado 1' },
                { rule: 'h2',   text: 'Encabezado 2' },
                { rule: 'h3',   text: 'Encabezado 3' },
                { rule: 'h4',   text: 'Encabezado 4' }
            ]
        },
        codeview: {
            enabled: true,
            showButton: true
        },
        images: {
            allowLocal : true
        },
        languages: ['en', 'es'],
        ui: {
            toolbar:  {
                items: [ 'undo', 'style', 'emphasis', 'align', 'listindent', 'format', 'tools' ]
            }
        }
    });

    $("#text-create-button").click(createText);
}

// Code: Page edit

function openEditPageDialog() {
    console.log("Editing page");
    $("#side-nav-button").sideNav("hide");
    $("#edit-page-dialog").modal("open");
}

function editPage() {
    pageName = $("#edit-page-name").val();
    pageCategory = $("#edit-page-category").val();
    $("#content").height($("#edit-page-height").val());
}

// Code: Image creation

function updatePreview() {
    var $url = $("#image-create-src");
    var $preview = $("#image-create-preview > img");
    var tmpImg = new Image();
    tmpImg.src = $url.val();

    $(tmpImg).one('load', function() {
        if (tmpImg.width == 0 || tmpImg.height == 0)
            invalidateImage();
        else {
            $url.removeClass("invalid").addClass("valid");
            $("#image-create-preview img").attr("src", $url.val());

            $("#image-create-width").html(tmpImg.width);
            $("#image-create-height").html(tmpImg.height);

            if(tmpImg.width > tmpImg.height)
                $preview.addClass("adjust-width").removeClass("adjust-height");
            else
                $preview.addClass("adjust-height").removeClass("adjust-width");
        }
    });

    $(tmpImg).one("error", function() {
        invalidateImage();
    });
}

function invalidateImage() {
    $url = $("#image-create-src");

    if($url.val() == "")
        $url.removeClass("invalid").removeClass("valid");
    else
        $url.removeClass("valid").addClass("invalid");

    $("#image-create-width, #image-create-height").html("200");
    $("#image-create-preview > img").attr("src", "no_image_selected.gif");
}

function openCreateDialog(type) {
    console.log("opening: #" + type + "-create-dialog");
    $("#" + type + "-create-dialog").modal("open");
    $(".sideNav-button").sideNav("hide");
}

function createImage() {
    console.log("Creating image");
    var url = $("#image-create-src").val();
    var attributes = {
        "src": url,
        "data-extension": url.slice(url.lastIndexOf('.')),
        "data-type": "image"
    };
    var css = {
        "width": $("#image-create-width").html() + "px",
        "height": $("#image-create-height").html() + "px"
    }
    newImages[nextID.toString()] = url;
    var $image = $("<img />")
    .attr(attributes)
    .css(css)
    .addClass("inner");
    createWrapper($image);
}

// Code: Text Creation

function createText() {
    console.log("Creating text");
    var text = $(textEditor.content.get());
    text.css("display", "inline-block");
    var $inner_content = $("<div></div>").append(text).addClass("inner-content").css("display", "inline-block");
    $("#content").append($inner_content);
    var attributes = {
        "data-type": "text"
    };
    var css = {
        "border": $("#text-create-border-style").val() + " " + 
                  $("#text-create-border-color").val() + " " +
                  $("#text-create-border-width").val(),
        "display": "inline-block",
        "padding": "0 10px",
        "width": $inner_content.width(),
        "height": $inner_content.height()
    };
    $inner_content.one("load", function() {
        $("#content").remove($inner_content);
    });
    var $text = $("<div></div>")
    .attr(attributes)
    .css(css)
    .append($inner_content)
    .addClass("inner");
    createWrapper($text);
}

// Code: Wrapper

function createWrapper($inner) {
    var $newElement = $("<div></div>").html($inner);
    $newElement.draggable({
        snap: true,
        scroll: false,
        containment: "#content"
    });
    $newElement.children(".handle").hide();
    $newElement.attr({
        "id": "object-" + nextID,
        "data-type": $inner.data("type")
    });
    $newElement.css({
        "width": $inner.outerWidth() + "px",
        "height": $inner.outerHeight() + "px",
        "float": "left",
        "position": "absolute !important"
    });
    if($inner.data("type") != "text") {
        $newElement.append($("<div id='handle-" + nextID + "' class='handle ui-resizable-handle ui-resizable-se'></div>")).resizable({
            handles: {
                "se": "#handle-" + nextID
            },
            aspectRatio: $newElement.width() / $newElement.height()
        });
    }
    $newElement.addClass("object " + $inner.data("type"));
    $newElement.children().css({
        "width": "100%",
        "height": "100%"
    });
    $newElement.on({
        "click" : function(e) {
            selectElement($(this));
            e.stopPropagation();
        },
        "mousedown" : function(e) {
            if(!dragging) {
                selectElement($(this));
                dragging = true;
                e.stopPropagation();
            }
        },
        "mouseup" : function(e) {
            dragging = false;
        }
    });
    $newElement.appendTo($("#content"));
    $newElement
    nextID++;
}

// Code: Selection

function selectElement($element) {
    unselectElement();
    console.log("selecting element");
    if($selectedElement.attr("id") != $element.attr("id")) {
        $selectedElement = $element;
        $selectedElement.addClass("selected");
        $selectedElement.children(".handle").show();
    }
}

function unselectElement() {
    console.log("unselecting");
    if($selectedElement[0].id != "#content") {
        $selectedElement.removeClass("selected");
        $selectedElement.children(".handle").hide();
    }
    $selectedElement = $("#content");
}

function editButtonClick() {
    if($selectedElement[0].id != "content")
        openEditDialog($selectedElement.data("type"));
    else
        openEditPageDialog();
}

function openEditDialog(type) {
    console.log("Editing " + type + ": " + $selectedElement.attr("id"));
}

// Code: Saving

function savePage() {
    console.log("Saving...");
    var $content = $("<div id=content></div>");
    var maxHeight = 0;
    var pageTranscript = "";
    $("#content").children(".object").each(function() {
        var type = $(this).data("type");
        var $inner = $(this).children(".inner");
        switch(type) {
            case "image":
                var $elem = $("<img />").attr({
                    "src": "/InteractED/post/content/" + postID + "/images/" + $(this).attr("id") + "." + $inner.data("extension"),
                    "width": $inner.width() + "px",
                    "height": $inner.height() + "px"
                });
                break;
            case "text":
                var $elem = $inner.children().clone();
                pageTranscript += $elem.text() + " ";
                break;
        }
        $elem.css({
            "position": "relative",
            "left": $(this).position().left,
            "top": $(this).position().top
        });
        var bottomPos = $(this).position().top + $(this).outerHeight(true);
        if(bottomPos > maxHeight)
        	maxHeight = bottomPos;
        $content.append($elem);
    });

    $content.css({
        "height": (maxHeight + 50) + "px",
        "width": $("#content").width() + "px"
    });

    var dataSaved = {
        id: postID,
        content: $content[0].outerHTML,
        transcript: pageTranscript,
        name: pageName,
        category: pageCategory,
        newImages: JSON.stringify(newImages)
    }

    if(debugSaveEnabled)
        console.log(dataSaved);
    else
        $.ajax({
            url: "save_page.php",
            type: "POST",
            data: dataSaved,
            success: function(result) {
                console.log(result);
                console.log("saved");
            },
            error: function() {
                console.log("saving error");
            }
        });
}

function toggleDebugSave() {
    debugSaveEnabled = !debugSaveEnabled;
    console.log("Debug save set to " + debugSaveEnabled);
}