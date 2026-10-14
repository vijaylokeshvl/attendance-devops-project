
from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait, Select
from selenium.webdriver.support import expected_conditions as EC

BRAVE_PATH = r"C:\Program Files\BraveSoftware\Brave-Browser\Application\brave.exe"

options = Options()
options.binary_location = BRAVE_PATH
options.add_argument("--window-size=1280,900")

driver = webdriver.Chrome(options=options)
wait = WebDriverWait(driver, 15)

try:
    driver.get("http://localhost:8081/login.php")

    wait.until(EC.visibility_of_element_located(
        (By.ID, "username")
    )).send_keys("admin")

    driver.find_element(By.ID, "password").send_keys("admin123")

    # The role dropdown is required by the form.
    Select(driver.find_element(By.ID, "role")).select_by_value("Admin")

    # Submit the form directly, using the real form element.
    form = driver.find_element(By.ID, "loginForm")
    driver.execute_script("arguments[0].requestSubmit();", form)

    wait.until(lambda d: "admin/dashboard.php" in d.current_url.lower())

    print("PASS: Admin login successful")
    print("Dashboard URL:", driver.current_url)

except Exception as e:
    print("FAIL:", type(e).__name__, str(e))
    print("Current URL:", driver.current_url)
    
    print("Page title:", driver.title)
    print("Page text:", driver.find_element(By.TAG_NAME, "body").text[:1500])
    print("Username entered:", driver.find_element(By.ID, "username").get_attribute("value"))
    print("Selected role:", Select(driver.find_element(By.ID, "role")).first_selected_option.get_attribute("value"))
    
    if "Invalid username or password" in driver.page_source:
        print("Login was rejected. Check the account and selected role.")

    driver.save_screenshot("selenium_failure.png")
    raise

finally:
    driver.quit()
