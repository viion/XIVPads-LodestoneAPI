//
// Hold achievement data
//
class AppAchievementViewClass
{
    constructor()
    {
        this.data = {};
        this.lodestoneId = null;
        this.server = null;
    }

    //
    // Set character lodestone id and achievement data
    //
    setData(lodestoneId, data, server)
    {
        this.data = data;
        this.lodestoneId = lodestoneId;
        this.server = server;
    }
}

// Export it
module.exports = new AppAchievementViewClass;
