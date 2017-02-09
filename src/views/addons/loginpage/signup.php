<?php get_header() ?>

    <section id="loginpage" class="signup">

        <h1><?php _e( 'Sign up' ) ?></h1>

        <loginpage-form inline-template
            action="<?php echo $action ?>"
            token="<?php echo $token ?>"
            :defaults="{redirect_to:'<?php echo $redirect_to ?>'}"
        />
            <form @submit.prevent="submit">

                <div class="form-group">
                    <label for="user_email"><?php _e( 'Email' ) ?></label>
                    <input type="email"
                        id="email"
                        class="form-control"
                        v-model="formData.user_email"
                    />
                </div>

                <div class="form-group">
                    <label for="user_login"><?php _e( 'Username' ) ?></label>
                    <input type="text"
                        id="user_login"
                        class="form-control"
                        v-model="formData.user_login"
                    />
                </div>

                <div class="form-group">
                    <label for="user_pass"><?php _e( 'Password' ) ?></label>
                    <input type="password"
                        id="user_pass"
                        class="form-control"
                        v-model="formData.user_pass"
                    />
                </div>

                <div class="form-group">
                    <label for="repeat_pass"><?php _e( 'Repeat password' ) ?></label>
                    <input type="password"
                        id="repeat_pass"
                        class="form-control"
                        v-model="formData.repeat_pass"
                    />
                </div>

                <!-- OPTIONAL -->
                <div class="form-group">
                    <label for="first_name"><?php _e( 'First name' ) ?></label>
                    <input type="text"
                        id="first_name"
                        class="form-control"
                        v-model="formData.first_name"
                    />
                </div>
                <!-- OPTIONAL -->

                <!-- OPTIONAL -->
                <div class="form-group">
                    <label for="first_name"><?php _e( 'Last name' ) ?></label>
                    <input type="text"
                        id="last_name"
                        class="form-control"
                        v-model="formData.last_name"
                    />
                </div>
                <!-- OPTIONAL -->

                <!-- OPTIONAL -->
                <div class="form-group">
                    <label for="first_name"><?php _e( 'Display name' ) ?></label>
                    <input type="text"
                        id="display_name"
                        class="form-control"
                        v-model="formData.display_name"
                    />
                </div>
                <!-- OPTIONAL -->

                <!-- OPTIONAL -->
                <div class="form-group">
                    <label for="user_url"><?php _e( 'Website' ) ?></label>
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
                    <div v-for="error in errors" v-html="error"></div>
                </section>
                <!-- IMPORTANT: Notifications must be placed anywhere inside id="signup" -->

                <!-- IMPORTANT: Notifications must be placed anywhere inside id="signup" -->
                <section class="success"
                    style="display: none;"
                    v-show="message"
                    v-html="message"
                ></section>
                <!-- IMPORTANT: Notifications must be placed anywhere inside id="signup" -->

                <button type="submit"
                    class="btn btn-default"
                    v-show="!isLoading"
                >
                    <?php _e( 'Signup' ) ?>
                </button>

                <span span="loading"
                    style="display: none;"
                    v-show="isLoading"
                >
                    <?php _e( 'Loading...' ) ?>
                </span>

            </form>
        </loginpage-form>

    </section>

<?php get_footer() ?>