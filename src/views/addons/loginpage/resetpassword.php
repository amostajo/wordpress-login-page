<?php get_header() ?>

    <section id="loginpage" class="resetpassword">

        <h1>Reset your password</h1>

        <p>This link will be invalid once finished.</p>

        <loginpage-form inline-template
            action="<?php echo $action ?>"
        />
            <form @submit.prevent="submit">

                <!-- IMPORTANT: Token must be present -->
                <input type="hidden"
                    value="<?php echo $token ?>"
                    v-model="formData._token"
                />
                <!-- IMPORTANT: Token must be present -->

                <!-- IMPORTANT: ID must be present -->
                <input type="hidden"
                    value="<?php echo $user->ID ?>"
                    v-model="formData.ID"
                />
                <!-- IMPORTANT: ID must be present -->

                <div class="form-group">
                    <label for="user_pass">New password</label>
                    <input type="password"
                        id="user_pass"
                        class="form-control"
                        v-model="formData.user_pass"
                    />
                </div>

                <div class="form-group">
                    <label for="user_login">Repeat password</label>
                    <input type="password"
                        id="repeat_pass"
                        class="form-control"
                        v-model="formData.repeat_pass"
                    />
                </div>

                <?php do_action( 'addon_loginpage_inside_reset_password_form' ) ?>

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
                    Reset
                </button>

                <span span="loading"
                    style="display: none;"
                    v-show="isLoading"
                >
                    Loading...
                </span>

            </form>
        </loginpage-form>

    </section>

<?php get_footer() ?>