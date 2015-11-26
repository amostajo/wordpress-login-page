/**
 * Login Page Add-on.
 * For Wordpress Development Templates.
 * VueJS plugin.
 *
 * @author Alejandro Mostajo
 * @license MIT
 * @version 1.0
 */
var loginpage = new Vue({

    el: '#loginpage',

    data: {
        /**
         * Error messages.
         * @since 1.0
         */
        errors: [],
        /**
         * Success message.
         * @since 1.0
         */
        message: '',
        /**
         * Form data.
         * @since 1.0
         */
        formData: {},
        /**
         * Flag that indicates if process is loading.
         * @since 1.0
         */
        isLoading: false
    },

    computed: {

        /**
         * Returns flag indicating if there are errors in form.
         * @since 1.0
         */
        hasErrors: function () {

            return Object.keys(this.errors).length > 0;

        }

    },

    methods: {
        /**
         * Performs ajax login.
         * @since 1.0
         */
        submit: function () {
            // Reset login data
            this.isLoading = true;
            this.message = '';
            this.errors = [];
            // This reference
            var self = this;
            // POST
            jQuery.post(
                jQuery(this.$el).find('form').attr('action'),
                this.formData,
                function(response) {
                    if (response.errors != undefined) {
                        self.errors = response.errors;
                    }
                    if (response.message != undefined) {
                        self.message = response.message;
                    }
                    if (response.redirect_to != undefined) {
                        window.location = response.redirect_to
                    }
                    // Restore loading
                    if (response.continue_loading == undefined
                        || !response.continue_loading
                    )
                        self.isLoading = false;
                }
            );
        }
    }
});