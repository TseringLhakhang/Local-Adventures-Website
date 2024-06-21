<?php
include 'top.php';
?>
<main>
    <section class='beige-half'>
        <h2 class="header-grid">Create Table SQL</h2>
        <pre>

CREATE TABLE tblUser(
    pmkUserId INT AUTO_INCREMENT PRIMARY KEY,
    fldUsername VARCHAR(50),
    fldFirstName VARCHAR(50),
    fldLastName VARCHAR(50),
    fldEmail VARCHAR(100),
    fldRegistrationDate DATETIME
);

CREATE TABLE tblCategory(
    pmkCategoryId INT AUTO_INCREMENT PRIMARY KEY,
    fldCategoryName VARCHAR(50)
); 

CREATE TABLE tblMunicipality(
    pmkMunicipalityId INT AUTO_INCREMENT PRIMARY KEY,
    fldMunicipalityName VARCHAR(100)
);

CREATE TABLE tblAdventure(
    pmkAdventureId INT AUTO_INCREMENT PRIMARY KEY,
    fnkOrganizerId INT,
    fnkCategoryId INT,
    fnkMunicipalityId INT,
    fldAddress VARCHAR(100),
    fldAdventureDescription VARCHAR(400),
    fldAdventureDate DATE,
    fldExpectedFee INT,
    fldAdventureImg VARCHAR(100),
    fldAdventureName VARCHAR(50),
    FOREIGN KEY(fnkOrganizerId) REFERENCES tblUser(pmkUserId),
    FOREIGN KEY(fnkCategoryId) REFERENCES tblCategory(pmkCategoryId),
    FOREIGN KEY(fnkMunicipalityId) REFERENCES tblCategory(pmkMunicipalityId)
); 

CREATE TABLE tblGroup(
    pmkGroupId INT AUTO_INCREMENT PRIMARY KEY,
    fldlGroupName VARCHAR(100),
    fldGroupLimit INT,
    fnkAdventureId INT,
    FOREIGN KEY(fnkAdventureId) REFERENCES tblAdventure(pmkAdventureId)
); 

CREATE TABLE tblUserGroupMember(
    pfkUserId INT,
    pfkGroupId INT,
    PRIMARY KEY(pfkUserId, pfkGroupId),
    FOREIGN KEY(pfkUserId) REFERENCES tblUser(pmkUserId),
    FOREIGN KEY(pfkGroupId) REFERENCES tblGroup(pmkGroupId)
); 

CREATE TABLE tblReview(
    pmkReviewID INT AUTO_INCREMENT PRIMARY KEY,
    fnkUserID INT,
    fnkAdventureId INT,
    fldDatePosted DATE,
    fldRating INT,
    fldReviewText VARCHAR(400),
    FOREIGN KEY(fnkUserId) REFERENCES tblUser(pmkUserId),
    FOREIGN KEY(fnkAdventureId) REFERENCES tblAdventure(pmkAdventureId)
);
        </pre>
    </section>
</main>
<?php
include 'footer.php';
?>