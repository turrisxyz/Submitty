// ***********************************************
// commands.js creates various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************
//
//
// -- This is a parent command --
// Cypress.Commands.add("login", (email, password) => { ... })
//
//
// -- This is a child command --
// Cypress.Commands.add("drag", { prevSubject: 'element'}, (subject, options) => { ... })
//
//
// -- This is a dual command --
// Cypress.Commands.add("dismiss", { prevSubject: 'optional'}, (subject, options) => { ... })
//
//
// -- This will overwrite an existing command --
// Cypress.Commands.overwrite("visit", (originalFn, url, options) => { ... })

import 'cypress-file-upload';
import {buildUrl} from './utils.js';
//These functions can be called like "cy.login(...)" and will yeild a result

/**
* Log into Submitty, assumes no one is logged in already and at login page
*
* @param {String} [username=instructor] - username & password of who to log in as
*/
Cypress.Commands.add('login', (username='instructor') => {
    cy.get('input[name=user_id]').should('be.visible')
    cy.get('input[name=password]').should('be.visible')
    cy.get('input[name=login]').should('be.visible')

    cy.get('input[name=user_id]').type(username);
    cy.get('input[name=password]').type(username);
    cy.get('input[name=login]').click();
    // check to see if we are logged in by looking to see if there are breadcrumbs
    cy.get('#nav-sidebar-my-courses').contains('My Courses')
});

/**
* Log out of Submitty, assumes a user is already logged in
*/
Cypress.Commands.add('logout', () => {
    cy.intercept('/authentication/logout').as('logoutSuccess')
    cy.get('#logout > .flex-line > .icon-title').click();
    // check to make sure we logged out correctly
    cy.wait('@logoutSuccess')
    cy.get('input[name=user_id]').should('be.visible')
    cy.get('input[name=password]').should('be.visible')
    cy.get('input[name=login]').should('be.visible')
});

/**
* Visit a url either by an array of parts or a completed url E.g:
* cy.vist(['sample', 'gradeables']) -> visit 'courses/s21/sample/gradeables'
* cy.vist('authentication/login') visit 'authentication/login'
*
* base url of localhost:1511 is used by default, see baseUrl in Cypress.json
*
* @param {String|String[]}
*/
Cypress.Commands.overwrite('visit', (originalFn, options) => {
    let url = '';

    if (Array.isArray(options)){
        url = buildUrl(options);
    }
    else if ((typeof options) === 'string'){
        url = options;
    }
    else {
        url = buildUrl([]);
    }

    originalFn(url);
});
