//
// Hold achievement data
//
class AppAchievementViewClass
{
    constructor()
    {
        this.data = {};
        this.lodestoneId = null;
    }

    //
    // Set character lodestone id and achievement data
    //
    setData(lodestoneId, data)
    {
        this.data = data;
        this.lodestoneId = lodestoneId;
    }
}

// Export it
module.exports = new AppAchievementViewClass;
