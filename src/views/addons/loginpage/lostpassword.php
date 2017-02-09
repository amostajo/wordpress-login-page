<?php get_header() ?>

    <section id="loginpage" class="lostpassword">

        <h1><?php _e( 'Lost your password?' ) ?></h1>

        <p><?php _e( 'Verify your information before proceeding.' ) ?></p>

        <loginpage-form inline-template
            action="<?php echo $action ?>"
            token="<?php echo $token ?>"
            :defaults="{redirect_to:'<?php echo $redirect_to ?>'}"
        />
            <form @submit.prevent="submit">

                <div class="form-group">
                    <label for="user_login"><?php _e( 'Username or Email' ) ?></label>
                    <input type="text"
                        id="user_login"
                        class="form-control"
                        v-model="formData.user_login"
                    />
                </div>

                <?php do_action( 'addon_loginpage_inside_lost_password_form' ) ?>

                <!-- IMPORTANT: Error notifications must be placed anywhere inside id="signup" -->
                <section class="errors"
                    style="display: none;"
                    v-show="hasErrors"
                >
                    <div v-for="error in errors">
                        {{ error }}
                    </div>
                </section>
                <!-- IMPORTANT: Notifications must be placed anywhere inside id="signup" -->

                <!-- IMPORTANT: Notifications must be placed anywhere inside id="signup" -->
                <section class="success"
                    style="display: none;"
                    v-show="message"
                >
                    {{ message }}
                </section>
                <!-- IMPORTANT: Notifications must be placed anywhere inside id="signup" -->

                <button type="submit"
                    class="btn btn-default"
                    v-show="!isLoading"
                >
                    <?php _e( 'Verify' ) ?>
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