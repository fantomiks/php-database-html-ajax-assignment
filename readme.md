# Test assignment

A small web application that touches on database, PHP, HTML/CSS and JavaScript/jQuery/AJAX.

This app should access a MySQL database table called ‘user’ that has the following columns:

- user_id
- name
- access_count
- modify_dt

That table should have several rows in it, each with distinct data.

The database should be accessed using an instance of a PHP class that manages the connection and provides methods for accessing the user table.

A PHP class that generates a page containing a tabular form should be written that includes all of the columns from the user table; it should list all of the rows.

Each row should have a button that, when clicked, uses AJAX to bump the access_count and update the modify_dt of the selected user using the server’s time.

The form should be updated to reflect the current access_count and modify_dt without reloading the page itself.

A page-level PHP script should be written that brings all of these elements together.

- [x] CSS should be used for styling;
- [x] Inline styles should be avoided.
- [x] This code should not be based on an existing PHP or JS framework (other than jQuery).
- [x] The code should be structured in a way that would easily accommodate either additional functionality to this page or additional pages without adding undue complexity to the current implementation.

---

## Start local environment:

Local environment uses docker-compose.

To start project, execute these commands:

```bash
docker-compose up -d
docker-compose exec app composer install
```

You are done. Goto http://localhost:9080
