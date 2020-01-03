package Client_Site;

import org.openqa.selenium.remote.DesiredCapabilities;
import org.testng.annotations.Test;
import org.testng.asserts.SoftAssert;
import org.openqa.selenium.Dimension;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.chrome.ChromeOptions;
import org.openqa.selenium.interactions.Actions;
import org.testng.Assert;
import org.testng.annotations.BeforeTest;
import org.testng.annotations.Optional;
import org.testng.annotations.Test;
import org.testng.annotations.Test;
import org.testng.asserts.SoftAssert;
import java.util.concurrent.TimeUnit;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;




import org.openqa.selenium.remote.DesiredCapabilities;

import org.testng.Assert;
import org.testng.annotations.*;
import org.testng.asserts.SoftAssert;

import java.util.concurrent.TimeUnit;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.chrome.ChromeDriver;
import org.openqa.selenium.firefox.FirefoxDriver;
import org.openqa.selenium.interactions.Actions;
import org.testng.Assert;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.JavascriptExecutor;	

import java.lang.*; 
import java.util.Properties;


public class Client {
	
	//public String baseUrl = "http://admin:mcfeely@dev1.mcfeelys.com";
	//public String baseUrl1 = "http://admin:mcfeely@dev1.mcfeelys.com/admin";
	public String baseUrl = System.getProperty("Client_Site"); 
	public String baseUrl1 = System.getProperty("Admin_Site");
	public String username = System.getProperty("Username"); 
	public String password = System.getProperty("Password");
	public DesiredCapabilities capability;
	public WebDriver driver ; 
	public String welcome;
	public Select customers;
	
public void resizeBrowser() {
  // Dimension d = new Dimension(1382,744);
  Dimension d = new Dimension(1910,1070);
   driver.manage().window().setSize(d);
}


@BeforeTest
public void deleteAccount() throws InterruptedException {
//System.setProperty("webdriver.chrome.driver", "C:\\Users\\HOME\\Desktop\\workplaceforall\\selenium\\new\\Chrome78\\chromedriver.exe");
	System.setProperty("webdriver.chrome.driver", "/bin/chromedriver");
	
	ChromeOptions options = new ChromeOptions();
	options.setHeadless(true);
	driver = new ChromeDriver(options);
	driver.get(baseUrl1);
	
    resizeBrowser();
	
	Thread.sleep(3000);
	
	driver.findElement(By.cssSelector("input#username")).sendKeys(username);
	driver.findElement(By.cssSelector("input#login")).sendKeys(password);
	driver.findElement(By.cssSelector(".action-login")).click();
	Thread.sleep(3000);
	driver.navigate().refresh();
	Thread.sleep(3000);
	driver.findElement(By.cssSelector("#menu-magento-customer-customer > a")).click();


    Actions mouseAction = new Actions(driver);
    mouseAction.moveToElement(driver.findElement(By.cssSelector("#menu-magento-customer-customer > a"))).perform();
    mouseAction.moveToElement(driver.findElement(By.linkText("All Customers"))).click().perform();
    Thread.sleep(15000);	
    
    try {
   	 
    driver.findElement(By.cssSelector(".admin__data-grid-filters-current:nth-child(4) .action-tertiary")).click();
    } catch (Exception e) { 
    }  
   
    Thread.sleep(3000);	
    driver.findElement(By.cssSelector("input#fulltext")).sendKeys("conbo@mailinator.com");
    driver.findElement(By.cssSelector(".data-grid-search-control-wrap:nth-child(2) > .action-submit")).click();
    
    Thread.sleep(3000);	
    
    try {
    	 
   	driver.findElement(By.cssSelector("td:nth-child(4) > .data-grid-cell-content")).click();
    driver.findElement(By.cssSelector(".col-xs-2 .action-select")).click();
    driver.findElement(By.cssSelector(".col-xs-2 .action-menu > li:nth-child(1) > .action-menu-item")).click();
    Thread.sleep(3000);
    driver.findElement(By.cssSelector(".action-accept > span")).click();
   
    } catch (Exception e) { 
   
    }
    
    driver.close();
		
}

	
@Test(priority=1)	
public void testcase1_createAccount() throws InterruptedException {
	//System.setProperty("webdriver.chrome.driver", "C:\\Users\\HOME\\Desktop\\workplaceforall\\selenium\\new\\Chrome78\\chromedriver.exe");
	System.setProperty("webdriver.chrome.driver", "/bin/chromedriver");
	ChromeOptions options = new ChromeOptions();
	options.setHeadless(true);
	driver = new ChromeDriver(options);
	driver.get(baseUrl);
	
	resizeBrowser();
	
	Thread.sleep(3000);
	
	
	driver.findElement(By.cssSelector(".header:nth-child(2) > li:nth-child(5) > a")).click();
	driver.findElement(By.cssSelector("input#firstname")).sendKeys("Con");
	driver.findElement(By.cssSelector("input#lastname")).sendKeys("Bo");
	driver.findElement(By.cssSelector("input#email_address")).sendKeys("conbo@mailinator.com");
	driver.findElement(By.cssSelector("input#password")).sendKeys("12345678s@S");
	driver.findElement(By.cssSelector("input#password-confirmation")).sendKeys("12345678s@S");
	driver.findElement(By.cssSelector(".actions-toolbar:nth-child(4) .submit > span")).click();
	
	Thread.sleep(3000);
	welcome = driver.findElement(By.cssSelector(".message-success > div")).getText();
	
	SoftAssert softAssert = new SoftAssert();
	
	softAssert.assertTrue(welcome.contains("Thank you for registering with McFeely's."));
	
	Thread.sleep(3000);
	driver.close();
	softAssert.assertAll();
}	


@Test(priority=2)	
public void testcase2_loginlogoutPage() throws InterruptedException {
	//System.setProperty("webdriver.chrome.driver", "C:\\Users\\HOME\\Desktop\\workplaceforall\\selenium\\new\\Chrome78\\chromedriver.exe");
	System.setProperty("webdriver.chrome.driver", "/bin/chromedriver");
	ChromeOptions options = new ChromeOptions();
    options.setHeadless(true);
    driver = new ChromeDriver(options);
    driver.get(baseUrl);

    resizeBrowser();
	
    Thread.sleep(3000);
	
	driver.findElement(By.cssSelector(".header:nth-child(2) > .authorization-link > a")).click();
	driver.findElement(By.cssSelector("input#email")).sendKeys("conbo@mailinator.com");
	driver.findElement(By.cssSelector("input#pass")).sendKeys("12345678s@S");
	driver.findElement(By.cssSelector(".primary:nth-child(1) > #send2 > span")).click();
	Thread.sleep(3000);
	welcome = driver.findElement(By.cssSelector(".header:nth-child(2) > .authorization-link:nth-child(5) > a")).getText();

	SoftAssert softAssert = new SoftAssert();
	softAssert.assertTrue(welcome.contains("SIGN OUT"));
	Thread.sleep(3000);
	
	driver.findElement(By.cssSelector(".header:nth-child(2) > .authorization-link:nth-child(5) > a")).click();
	Thread.sleep(3000);
	welcome = driver.findElement(By.cssSelector(".base")).getText();
	
	softAssert.assertEquals(welcome,"You are signed out");
	
	driver.close();
	softAssert.assertAll();
}




@Test(priority=3)	
public void testcase3_searchProduct() throws InterruptedException {
	   // System.setProperty("webdriver.chrome.driver", "C:\\Users\\HOME\\Desktop\\workplaceforall\\selenium\\new\\Chrome78\\chromedriver.exe");
	    System.setProperty("webdriver.chrome.driver", "/bin/chromedriver");
	    ChromeOptions options = new ChromeOptions();
		options.setHeadless(true);
		driver = new ChromeDriver(options);
		driver.get(baseUrl);
		
		resizeBrowser();
		
		Thread.sleep(3000);
		
		driver.findElement(By.cssSelector("input#search")).sendKeys("woodpeackers 32");
		Thread.sleep(3000);
		driver.findElement(By.cssSelector(".klevu-name-l2")).click();
		Thread.sleep(3000);
		welcome = driver.findElement(By.cssSelector(".base")).getText();
		SoftAssert softAssert = new SoftAssert();
		softAssert.assertTrue(welcome.contains("Woodpeckers 32 in T-Square"));
		
		driver.close();
		softAssert.assertAll();
		
}



@Test(priority=4)	
public void testcase4_addtoCart_HomePage() throws InterruptedException {
	   // System.setProperty("webdriver.chrome.driver", "C:\\Users\\HOME\\Desktop\\workplaceforall\\selenium\\new\\Chrome78\\chromedriver.exe");
	    System.setProperty("webdriver.chrome.driver", "/bin/chromedriver");
	    ChromeOptions options = new ChromeOptions();
		options.setHeadless(true);
		driver = new ChromeDriver(options);
		driver.get(baseUrl);
		
		resizeBrowser();
		
		Thread.sleep(3000);
		
		Actions mouseAction = new Actions(driver);
		   
		mouseAction.moveToElement(driver.findElement(By.cssSelector(".ui-menu-item:nth-child(2) > .level-top > span"))).perform();

		mouseAction.moveToElement(driver.findElement(By.linkText("NAILS - NAIL GUN NAILS, BRADS, PINS"))).click().perform();
		Thread.sleep(5000);	
			
		driver.findElement(By.cssSelector(".item:nth-child(1) .actions-primary span")).click();
		Thread.sleep(5000);	
		
	    welcome = driver.findElement(By.cssSelector(".message-success > div")).getText();

		SoftAssert softAssert = new SoftAssert();
			
		softAssert.assertTrue(welcome.contains("You added"));
		
		driver.close();
	    softAssert.assertAll();
				
}


@Test(priority=5)	
public void testcase5_addtoCart_ProductPage() throws InterruptedException {
	   // System.setProperty("webdriver.chrome.driver", "C:\\Users\\HOME\\Desktop\\workplaceforall\\selenium\\new\\Chrome78\\chromedriver.exe");
	    System.setProperty("webdriver.chrome.driver", "/bin/chromedriver");
	    ChromeOptions options = new ChromeOptions();
		options.setHeadless(true);
		driver = new ChromeDriver(options);
		driver.get(baseUrl);
		
		resizeBrowser();
		
		Thread.sleep(3000);
		
		Actions mouseAction = new Actions(driver);
		   
		mouseAction.moveToElement(driver.findElement(By.cssSelector(".ui-menu-item:nth-child(2) > .level-top > span"))).perform();

		mouseAction.moveToElement(driver.findElement(By.linkText("NAILS - NAIL GUN NAILS, BRADS, PINS"))).click().perform();
		Thread.sleep(5000);	
			
		driver.findElement(By.cssSelector(".item:nth-child(2) .product-image-photo")).click();
		driver.findElement(By.cssSelector("#product-addtocart-button > span")).click();
		Thread.sleep(5000);	
		welcome = driver.findElement(By.cssSelector(".message-success > div")).getText();
		SoftAssert softAssert = new SoftAssert();
		softAssert.assertTrue(welcome.contains("You added"));
		
		driver.close();
	    softAssert.assertAll();
		
		
		
		
		
}





























   
  
}
