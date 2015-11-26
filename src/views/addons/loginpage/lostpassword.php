<?php get_header() ?>

    <section id="loginpage" class="lostpassword">

        <h1>Lost your password?</h1>

        <p>Verify your information before proceeding.</p>

        <form action="<?php echo $action ?>"
            @submit.prevent="submit"
        />
            <!-- IMPORTANT: Token must be present -->
            <input type="hidden"
                value="<?php echo $token ?>"
                v-model="formData._token"
            />
            <!-- IMPORTANT: Token must be present -->

            <!-- OPTIONAL: Added redirect to after login -->
            <input type="hidden"
                value="<?php echo $redirect_to ?>"
                v-model="formData.redirect_to"
            />
            <!-- OPTIONAL: Added redirect to after login -->

            <div class="form-group">
                <label for="user_login">Username or Email</label>
                <input type="text"
                    id="user_login"
                    class="form-control"
                    v-model="formData.user_login"
                />
            </div>

            <!-- IMPORTANT: Error notifications must be placed anywhere inside id="signup" -->
            <section class="errors"
                style="display: none;"
                v-show="hasErrors"
            >
                <div v-for="error in errors">
                    {{{ error }}}
                </div>
            </section>
            <!-- IMPORTANT: Notifications must be placed anywhere inside id="signup" -->


            <!-- IMPORTANT: Notifications must be placed anywhere inside id="signup" -->
            <section class="success"
                style="display: none;"
                v-show="message"
            >
                {{{ message }}}
            </section>
            <!-- IMPORTANT: Notifications must be placed anywhere inside id="signup" -->

            <button type="submit"
                class="btn btn-default"
                v-show="!isLoading"
            >
                Verify
            </button>

            <span span="loading"
                style="display: none;"
                v-show="isLoading"
            >
                Loading...
            </span>

        </form>

    </section>

<?php get_footer() ?>