<?php get_header() ?>

    <section id="loginpage" class="login">

        <h1>Login</h1>

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

                <!-- OPTIONAL: Added redirect to after login -->
                <input type="hidden"
                    value="<?php echo $redirect_to ?>"
                    v-model="formData.redirect_to"
                />
                <!-- OPTIONAL: Added redirect to after login -->

                <div class="form-group">
                    <label for="user_login">Username</label>
                    <input type="text"
                        id="user_login"
                        class="form-control"
                        v-model="formData.user_login"
                    />
                </div>

                <div class="form-group">
                    <label for="user_password">Password</label>
                    <input type="password"
                        id="user_password"
                        class="form-control"
                        v-model="formData.user_password"
                    />
                </div>

                <div class="checkbox">
                    <label for="remember">
                        <input type="checkbox"
                            id="remember"
                            value="1"
                            v-model="formData.remember"
                        /> Remember me
                    </label>
                </div>

                <?php do_action( 'addon_loginpage_inside_login_form' ) ?>

                <!-- IMPORTANT: Notifications must be placed anywhere inside id="login" -->
                <section class="errors"
                    style="display: none;"
                    v-show="hasErrors"
                >
                    <div v-for="error in errors">
                        {{ error }}
                    </div>
                </section>
                <!-- IMPORTANT: Notifications must be placed anywhere inside id="login" -->

                <button type="submit"
                    class="btn btn-default"
                    v-show="!isLoading"
                >
                    Login
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