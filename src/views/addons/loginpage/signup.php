<?php get_header() ?>

    <section id="loginpage" class="signup">

        <h1>Sign up</h1>

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
                <label for="user_email">Email</label>
                <input type="email"
                    id="email"
                    class="form-control"
                    v-model="formData.user_email"
                />
            </div>

            <div class="form-group">
                <label for="user_login">Username</label>
                <input type="text"
                    id="user_login"
                    class="form-control"
                    v-model="formData.user_login"
                />
            </div>

            <div class="form-group">
                <label for="user_pass">Password</label>
                <input type="password"
                    id="user_pass"
                    class="form-control"
                    v-model="formData.user_pass"
                />
            </div>

            <div class="form-group">
                <label for="repeat_pass">Repeat password</label>
                <input type="password"
                    id="repeat_pass"
                    class="form-control"
                    v-model="formData.repeat_pass"
                />
            </div>

            <!-- OPTIONAL -->
            <div class="form-group">
                <label for="first_name">First name</label>
                <input type="text"
                    id="first_name"
                    class="form-control"
                    v-model="formData.first_name"
                />
            </div>
            <!-- OPTIONAL -->

            <!-- OPTIONAL -->
            <div class="form-group">
                <label for="first_name">Last name</label>
                <input type="text"
                    id="last_name"
                    class="form-control"
                    v-model="formData.last_name"
                />
            </div>
            <!-- OPTIONAL -->

            <!-- OPTIONAL -->
            <div class="form-group">
                <label for="first_name">Display name</label>
                <input type="text"
                    id="display_name"
                    class="form-control"
                    v-model="formData.display_name"
                />
            </div>
            <!-- OPTIONAL -->

            <!-- OPTIONAL -->
            <div class="form-group">
                <label for="user_url">Website</label>
                <input type="text"
                    id="user_url"
                    class="form-control"
                    v-model="formData.user_url"
                />
            </div>
            <!-- OPTIONAL -->

            <?php do_action( 'addon_loginpage_inside_signup_form' ) ?>

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
                Signup
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