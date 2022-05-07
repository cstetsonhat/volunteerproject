<!-- 

/**
 * @author [Cathleen Stetson, IT4400, Final Project]
 * @email [cpm4bf@virginia.edu]
 * @create date 2022-05-06 18:54:44
 * @modify date 2022-05-06 18:55:47
 */
 -->

<form action="index.php" method="POST" name="form_signin" class="d-flex flex-col c-m-t gap border c-padding" style="--c-m-t:3rem;--gap:1rem;--c-padding: 0.8rem">

    <div class="form_input d-flex justify-between">
        <label for="">email
        </label>
        <input type="email" required placeholder="email@domain.com" name="email" value="email@domain.com">
    </div>


    <div class="form_input d-flex justify-between">
        <label for="">password
        </label>
        <input type="password" required name="password" value="password">
    </div>


    <div class="form_input d-flex justify-between">
        <button type="submit" value="sign_in" name="action" class="bg-primary btn">Sign in</button>

        <a href="signup.php">Signup</a>
    </div>


</form>