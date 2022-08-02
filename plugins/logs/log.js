jQuery(document).ready(function ($) {
  /**
   * Variables from WordPress, passed using wp_localize_script():
   * theme_uri - URI to the theme folder for TT Child.
   * rest_url  - URL for the REST API root route.
   */
  const themeURI = postdata.theme_uri;
  const restURL = postdata.rest_url;

  /**
   * Builds out the HTML of the new log post.
   * @param {object} logObject - modified log object with available content
   */
  function buildNewList(logObject) {
    document.querySelector("#main .log").remove();

    if (logObject) {
      $.each(logObject, function (i) {
        $("#main").append(
          ` <article id="post-${logObject[i].id}" class="type-log status-publish hentry entry">
          <header class="entry-header">
            <h2 class="entry-title default-max-width"><a href="${logObject[i].guid.rendered}">${logObject[i].title.rendered}</a></h2>
          </header>
    
          <div class="entry-content">
            <p>${logObject[i].content.rendered}</p>
          </div>
    
          <footer class="entry-footer default-max-width"></footer>
        </article>`
        );
      });
    } else {
      $("#main").append(` <article><p>Logs not found</p></article>`);
    }
  }

  // Users select options
  function buildUsersList(usersObject) {
    $.each(usersObject, function (i) {
      $("#users").append(
        `<option id="${usersObject[i].id}">${usersObject[i].name}</option>`
      );
    });
  }

  // Api Get Request
  const getContent = (queryURL) => {
    fetch(queryURL)
      .then((response) => response.json())
      .then((logObject) => buildNewList(logObject));
  };
  // Get logs by date and time
  const getLogsByDateTime = (dateTime) => {
    // https://example.com/wp-json/wp/v2/logs?after=before=${dateTime};
    let dateTimeURL = `${restURL}log?before=${dateTime}&_embed`;
    getContent(dateTimeURL);
  };

  const getLogsByAuthor = (authorId) => {
    //https://example.com/wp-json/wp/v2/logs?author=${authorId}
    let LogsByAuthorURL = `${restURL}log?=?author=${authorId}`;
    getContent(LogsByAuthorURL);
  };

  $("#datepicker").datepicker();
  $("#datepicker").datepicker("option", "dateFormat", "yy-mm-dd 00:00:00");
  $("#datepicker").on("change", function () {
    getLogsByDateTime($(this).val());
  });

  // Get Posts By Author
  $("#users").on("change", function () {
    let authorId = $(this).children(":selected").attr("id");
    getLogsByAuthor(authorId);
  });

  // Get all users from the backend
  const getUsers = () => {
    let UsersURL = `${restURL}users`;
    fetch(UsersURL)
      .then((response) => response.json())
      .then((usersObject) => buildUsersList(usersObject));
  };
  getUsers();
});
