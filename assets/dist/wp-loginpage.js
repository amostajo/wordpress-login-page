/**
 * Vue component for package forms.
 * Vue Component & jQuery
 *
 * @author Alejandro Mostajo
 * @version 2.0.3
 * @license MIT
 */
Vue.component('loginpage-form', {
    /**
     * Default template.
     * @since 2.0.1
     */
    template: '<form @submit="submit"><slot></slot></form>',
    /**
     * Properties.
     * @since 2.0.1
     */
    props:
    {
        /**
         * Default form action.
         * @since 2.0.1
         * @var string
         */
        action:
        {
            type: String,
            default: undefined,
        },
        /**
         * Security token.
         * @since 2.0.3
         * @var string
         */
        token:
        {
            type: String,
            default: undefined,
        },
        /**
         * Default data.
         * @since 2.0.3
         * @var string
         */
        defaults:
        {
            type: Object,
            default: undefined,
        },
    },
    /**
     * Data.
     * @since 2.0.1
     */
    data: function()
    {
        return {
            /**
             * Error messages.
             * @since 2.0.1
             * @var mixed
             */
            errors: [],
            /**
             * Success message.
             * @since 2.0.1
             * @var String
             */
            message: '',
            /**
             * Form data.
             * @since 1.0
             * @var object
             */
            formData: {},
            /**
             * Flag that indicates if process is loading.
             * @since 2.0.1
             * @var bool
             */
            isLoading: false,
        };
    },
    /**
     * Computed.
     * @since 2.0.1
     */
    computed:
    {
        /**
         * Returns flag indicating if there are errors in form.
         * @since 2.0.1
         * @var bool
         */
        hasErrors: function()
        {
            return Object.keys(this.errors).length > 0;
        },
        /**
         * Returns post request data.
         * @since 2.0.3
         * @var object
         */
        request: function()
        {
            var request = {
                _token: this.token
            };
            // (1) defaults
            for (var key in this.defaults) {
                request[key] = this.defaults[key];
            }
            // (2) form data
            for (var key in this.formData) {
                request[key] = this.formData[key];
            }
            return request;
        },
    },
    /**
     * Methods.
     * @since 2.0.1
     */
    methods: {
        /**
         * Performs ajax login.
         * @since 2.0.1
         * @since 2.0.3 Double click prevent and request.
         */
        submit: function ()
        {
            // Prevent double clicking
            if (this.isLoading)
                return;
            // Reset login data
            this.isLoading = true;
            this.message = '';
            this.errors = [];
            // POST
            jQuery.post(
                this.action,
                this.request,
                this.onSubmit
            );
        },
        /**
         * Handles submit response.
         * @since 2.0.1
         *
         * @param object response.
         */
        onSubmit: function(response)
        {
            if (response.errors != undefined) {
                this.errors = response.errors;
            }
            if (response.message != undefined) {
                this.message = response.message;
            }
            if (response.redirect_to != undefined) {
                window.location = response.redirect_to
            }
            // Restore loading
            if (response.continue_loading == undefined
                || !response.continue_loading
            )
                this.isLoading = false;
        },
    },
});